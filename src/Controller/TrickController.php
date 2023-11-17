<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\UX\Turbo\TurboBundle;

class TrickController extends AbstractController
{
    public function __construct(
        private SluggerInterface $slugger,
        private EntityManagerInterface $em
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
            $trick->setUser($this->em->getRepository(User::class)->findOneBy(['name' => 'Deux']));

            foreach ($trick->getMedia() as $key => $medium) {
                $file = $form['media'][$key]['file']->getData();
                if ($file) {
                    $this->saveFile($file, $medium);
                }
            }

            $this->em->persist($trick);
            $this->em->flush();

            return $this->redirectToRoute('app_trick', ['slug' => $trick->getSlug()]);
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

    #[Route('/trick/supprimer/{slug}', name: 'app_trick_delete')]
    public function delete(Trick $trick, Request $request, EntityManagerInterface $em)
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

    #[Route('/trick/{slug}', name: 'app_trick')]
    public function index(Request $request, Trick $trick): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('app_trick', ['slug' => $trick->getSlug()])
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->em->getRepository(User::class)->findOneBy(['name' => 'Deux']));
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

        $params = [
            'trick' => $trick,
            'action' => 'show',
            'comments' => $comments,
            'form' => $form,
        ];
        if ($request->query->get('after-edition')) {
            $params['turbo-action'] = 'update';
            $params['turbo-target'] = 'trick-modal';
        }
        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('trick/stream/_modal.stream.html.twig', $params);
    }

    #[Route('/trick/{slug}/edit', name: 'app_trick_edit')]
    public function edit(Request $request, EntityManagerInterface $em, Trick $trick): Response
    {
        $form = $this->createForm(TrickType::class, $trick, [
            'action' => $this->generateUrl('app_trick_edit', ['slug' => $trick->getSlug()])
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

            return $this->redirectToRoute('app_trick', [
                'slug' => $trick->getSlug(),
                'after-edition' => true,
            ], 303);
        }

        $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
        return $this->render('trick/stream/_modal.stream.html.twig', [
            'trick' => $trick,
            'form' => $form,
            'action' => 'edit',
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
