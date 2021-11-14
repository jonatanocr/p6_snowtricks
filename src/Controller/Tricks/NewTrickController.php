<?php

namespace App\Controller\Tricks;

use App\Entity\Trick;
use App\Entity\User;
use App\Form\Tricks\NewTrickFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class NewTrickController extends AbstractController
{
    /**
     * @Route("/newTrick", name="new_trick")
     */
    public function newTrick(Request $request, Security $security): Response
    {
        $trick = new Trick();
        $user = $security->getUser();
        $form = $this->createForm(NewTrickFormType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setAuthor($user);
            $trick->setCreatedDate(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();
            $this->addFlash('success', 'New trick is added');
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('tricks/new.html.twig', [
            'newTrickForm' => $form->createView(),
        ]);
    }
}