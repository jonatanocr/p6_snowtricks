<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class Test extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index() {
        return $this->render('test/index.html.twig', ['variable' => ' ( ⚆ _ ⚆ ) ']);
    }
}