<?php


namespace App\Controller\Tricks;


use App\Entity\Trick;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class DeleteTrickController extends AbstractController
{
    /**
     * @Route("/trick_delete/{id}", name="trick_delete")
     */
    public function index(Request $request, int $id): Response
    {
        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        if ($trick === null) {
            $this->addFlash('danger', 'An error occured ( ⚆ _ ⚆ )');
        } else {
            $entityManager->remove($trick);
            $entityManager->flush();
            $this->addFlash('danger', 'Trick successfully deleted');
        }
        return $this->redirectToRoute('app_homepage');
    }
}