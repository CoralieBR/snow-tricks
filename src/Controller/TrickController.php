<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    #[Route('/trick/{slug}', name: 'app_trick')]
    public function index(string $slug, Request $request, TrickRepository $trickRepository, GroupRepository $groupRepository): Response
    {
        $trick = $trickRepository->findOneBy(['slug' => $slug]);
        $groups = $groupRepository->findAll();

        $frameId = $request->headers->get('Turbo-Frame');

        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
            'groups' => $groups,
            'isModal' => $frameId ? true : false,
        ]);
    }

    #[Route('/trick/{slug}/edit', name: 'app_trick_edit')]
    public function edit(string $slug,Request $request, TrickRepository $trickRepository, GroupRepository $groupRepository): Response
    {
        $trick = $trickRepository->findOneBy(['slug' => $slug]);
        $groups = $groupRepository->findAll();

        $frameId = $request->headers->get('Turbo-Frame');

        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
            'groups' => $groups,
            'isModal' => $frameId ? true : false,
            'edit' => true,
        ]);
    }
}
