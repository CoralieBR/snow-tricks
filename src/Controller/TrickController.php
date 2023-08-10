<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    #[Route('/trick/{slug}', name: 'app_trick')]
    public function index(string $slug, TrickRepository $trickRepository, GroupRepository $groupRepository): Response
    {
        $trick = $trickRepository->findOneBy(['slug' => $slug]);
        $groups = $groupRepository->findAll();

        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
            'groups' => $groups,
        ]);
    }
}
