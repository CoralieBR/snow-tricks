<?php

namespace App\Controller;

use App\Entity\{Comment, Trick};
use App\Form\{CommentType, TrickType};
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\{Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\UX\Turbo\TurboBundle;

class TrickController extends AbstractController
{
    public function __construct(
        private SluggerInterface $slugger,
        private EntityManagerInterface $em,
        private Security $security,
    ) {}

    #[Route('/trick/ajouter', name: 'app_trick_new')]
    public function new(Request $request)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick, [
            'action' => $this->generateUrl('app_trick_new')
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setSlug($this->slugger->slug($trick->getName()));
            $trick->setUser($this->security->getUser());

            foreach ($trick->getMedia() as $key => $medium) {
                $file = $form['media'][$key]['file']->getData();
                if ($file) {
                    $this->saveFile($file, $medium);
                }
            }

            $this->em->persist($trick);
            $this->em->flush();

            $this->addFlash(
                'notice',
                'Vous avez bien créé la figure ' . $trick->getName() . '!'
            );

            return $this->redirectToRoute('app_trick', [
                'slug' => $trick->getSlug(),
                'id' => $trick->getId(),
            ]);
        }

        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('trick/stream/_modal.stream.html.twig', [
            'action' => 'new',
            'form' => $form,
        ]);
    }

    #[Route('/trick/fermer', name:'app_trick_modal_close')]
    public function close(Request $request)
    {
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('trick/stream/close-modal.stream.html.twig');
    }

    #[Route('/trick/supprimer/{slug}/{id}', name: 'app_trick_delete')]
    public function delete(Trick $trick, Request $request)
    {
        $id = $trick->getId();
        $name = $trick->getName();
        $this->em->remove($trick);
        $this->em->flush();

        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('trick/stream/deleted.stream.html.twig', [
            'id' => $id,
            'name' => $name,
        ]);

    }
    #[Route('/trick/{slug}/{id}/show-media', name: 'show_media')]
    public function showMedia(Trick $trick, Request $request)
    {
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('trick/stream/media.stream.html.twig', [
            'media' => $trick->getMedia()
        ]);
    }

    #[Route('/trick/{slug}/{id}', name: 'app_trick')]
    public function index(Request $request, Trick $trick): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('app_trick', [
                'slug' => $trick->getSlug(),
                'id' => $trick->getId()
            ])
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->security->getUser());
            $comment->setTrick($trick);

            $this->em->persist($comment);
            $this->em->flush();

            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
            return $this->render('trick/stream/new-comment.stream.html.twig', [
                'comment' => $comment,
                'commentsCount' => count($this->em->getRepository(Comment::class)->findBy(['trick' => $trick->getId()])),
            ]);
        }

        $comments = $this->em->getRepository(Comment::class)->findBy(['trick' => $trick->getId()]);

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'action' => 'show',
            'comments' => $comments,
            'form' => $form,
        ]);
    }

    #[Route('/trick/{slug}/{id}/edit', name: 'app_trick_edit')]
    public function edit(Request $request, Trick $trick): Response
    {
        $form = $this->createForm(TrickType::class, $trick, [
            'action' => $this->generateUrl('app_trick_edit', [
                'slug' => $trick->getSlug(),
                'id' => $trick->getId(),
            ])
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setUpdatedAt(new \DateTimeImmutable());

            foreach ($trick->getMedia() as $key => $medium) {
                $file = $form['media'][$key]['file']->getData();
                if ($file) {
                    $this->saveFile($file, $medium);
                }
            }

            $this->em->flush();

            $this->addFlash(
                'notice',
                'Vous avez bien modfié la figure ' . $trick->getName() . '!'
            );

            return $this->redirectToRoute('app_trick', [
                'slug' => $trick->getSlug(),
                'id' => $trick->getId(),
                'after-edition' => true,
            ], 303);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    private function saveFile($file, $medium)
    {
        $safeFileName = $this->slugger->slug($file->getClientOriginalName());
        $newFileName = $safeFileName . '-' . uniqid() . '.' . $file->guessExtension();
        $file->move($this->getParameter('media_directory'), $newFileName);
        $medium->setPath($newFileName);
    }
}
