<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class OtherController extends AbstractController
{
    #[Route('/autre-page', name: 'other_page')]
    public function index()
    {
        return $this->render('other/other.html.twig');
    }
}
