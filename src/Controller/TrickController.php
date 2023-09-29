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

class TrickController extends AbstractController
{
    #[Route('/trick/ajouter', name: 'app_trick_new')]
    public function new(Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
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

            return $this->redirectToRoute('app_homepage', [], 303);
        }

        $frameId = $request->headers->get('Turbo-Frame');
        return $this->render('trick/new.html.twig', [
            'isModal' => $frameId ? true : false,
            'form' => $form
        ]);
    }

    #[Route('/trick/{slug}', name: 'app_trick')]
    public function index(Request $request, Trick $trick): Response
    {
        $frameId = $request->headers->get('Turbo-Frame');

        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
            'isModal' => $frameId ? true : false,
        ]);
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

            return $this->redirectToRoute('app_trick', ['slug' => $trick->getSlug()], 303);
        }

        $frameId = $request->headers->get('Turbo-Frame');
        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
            'isModal' => $frameId ? true : false,
            'edit' => true,
            'form' => $form
        ]);
    }
}
