<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\GroupRepository;
use App\Repository\TrickRepository;
use App\Repository\UserRepository;
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
    public function new(Request $request, SluggerInterface $slugger, EntityManagerInterface $em, UserRepository $userRepository): Response
    {
        $form = $this->createForm(TrickType::class, new Trick(), [
            'action' => $this->generateUrl('app_trick_new')
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();
            $trick->setCreatedAt(new \DateTimeImmutable());
            $trick->setSlug($slugger->slug($trick->getName()));
            $trick->setUser($userRepository->findOneBy(['name' => 'Deux']));

            foreach ($trick->getGroups() as $group) {
                $group->addTrick($trick);
                $em->persist($group);
            }

            $em->persist($trick);
            $em->flush();

            if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                // If the request comes from Turbo, set the content type as text/vnd.turbo-stream.html and only send the HTML to update
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('trick/success.stream.html.twig', ['trick' => $trick]);
            }

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
    public function edit(Request $request, EntityManagerInterface $em, $slug, Trick $trick, GroupRepository $groupRepository): Response
    {
        $groups = $groupRepository->findAll();

        $form = $this->createForm(TrickType::class, $trick, [
            'action' => $this->generateUrl('app_trick_edit', ['slug' => $slug])
        ]);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trick = $form->getData();
            $trick->setUpdatedAt(new \DateTimeImmutable());

            foreach ($trick->getGroups() as $group) {
                $group->addTrick($trick);
                $em->persist($group);
            }

            $em->persist($trick);
            $em->flush();

            return $this->redirectToRoute('app_trick', ['slug' => $slug], 303);
        }

        $frameId = $request->headers->get('Turbo-Frame');
        return $this->render('trick/index.html.twig', [
            'trick' => $trick,
            'groups' => $groups,
            'isModal' => $frameId ? true : false,
            'edit' => true,
            'form' => $form
        ]);
    }
}
