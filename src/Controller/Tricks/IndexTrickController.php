<?php

namespace App\Controller\Tricks;

use App\Entity\Trick;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexTrickController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index()
    {
        $tricks = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->findAll();
        return $this->render('tricks/index.html.twig', ['tricks' => $tricks]);
    }

    /**
     * @Route("/trick/{id}", name="trick_display")
     */
    public function displayOne(int $id): Response
    {
        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->find($id);
        if (!$trick) {
            throw $this->createNotFoundException(
                'No trick found'
            );
        }
        return $this->render('tricks/one.html.twig', ['trick' => $trick]);
    }
}
