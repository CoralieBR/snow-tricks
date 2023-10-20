<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\User;
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
    #[Route('/trick/ajouter', name: 'app_trick_new')]
    public function new(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick, [
            'action' => $this->generateUrl('app_trick_new')
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setSlug($slugger->slug($trick->getName()));
            $trick->setUser($em->getRepository(User::class)->findOneBy(['name' => 'Deux']));

            $em->persist($trick);
            $em->flush();

            return $this->redirectToRoute('app_trick', ['slug' => $trick->getSlug()]);
        }

        try {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
            return $this->render('trick/stream/_modal.stream.html.twig', [
                'action' => 'new',
                'form' => $form,
            ]);
        } catch (\Throwable $th) {
            return $this->render('trick/new.html.twig', [
                'form' => $form,
            ]);
        }
    }

    #[Route('/trick/fermer', name:'app_trick_modal_close')]
    public function close(Request $request)
    {
        try {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
            return $this->render('trick/stream/close-modal.stream.html.twig');
        } catch (\Throwable $th) {
            return $this->redirectToRoute('app_homepage');
        }
    }

    #[Route('/trick/supprimer/{slug}', name: 'app_trick_delete')]
    public function delete(Trick $trick, Request $request, EntityManagerInterface $em)
    {
        $id = $trick->getId();
        $name = $trick->getName();
        $em->remove($trick);
        $em->flush();

        try {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
            return $this->render('trick/stream/deleted.stream.html.twig', [
                'id' => $id,
                'name' => $name,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    #[Route('/trick/{slug}', name: 'app_trick')]
    public function index(Request $request, Trick $trick): Response
    {
        $params = [
            'trick' => $trick,
            'action' => 'show',
        ];
        if ($request->query->get('after-edition')) {
            $params['turbo-action'] = 'update';
            $params['turbo-target'] = 'trick-modal';
        }
        try {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
            return $this->render('trick/stream/_modal.stream.html.twig', $params);
        } catch (\Throwable $th) {
            return $this->render('trick/show.html.twig', [
                'trick' => $trick,
                'isModal' => false,
            ]);
        }
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

            $em->flush();

            return $this->redirectToRoute('app_trick', [
                'slug' => $trick->getSlug(),
                'after-edition' => true,
            ], 303);
        }

        try {
            $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
            return $this->render('trick/stream/_modal.stream.html.twig', [
                'trick' => $trick,
                'form' => $form,
                'action' => 'edit',
            ]);
        } catch (\Throwable $th) {
            return $this->render('trick/edit.html.twig', [
                'trick' => $trick,
                'isModal' => false,
                'form' => $form
            ]);
        }
    }
}
