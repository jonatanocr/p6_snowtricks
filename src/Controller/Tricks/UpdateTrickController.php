<?php


namespace App\Controller\Tricks;


use App\Entity\Trick;
use App\Form\UpdateTrickFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UpdateTrickController extends AbstractController
{
    /**
     * @Route("/trick_update/{id}", name="trick_update")
     */
    public function updateTrick(Request $request, int $id): Response
    {
        $trick = new Trick();
        $entityManager = $this->getDoctrine()->getManager();
        $trick = $entityManager->getRepository(Trick::class)->find($id);
        $updateTrick = $this->createForm(UpdateTrickFormType::class, $trick);
        $updateTrick->handleRequest($request);
        if ($updateTrick->isSubmitted() && $updateTrick->isValid()) {
            $trick->setLastUpdatedDate(new \DateTime());
            $entityManager->persist($trick);
            $entityManager->flush();
            $this->addFlash('success', 'Trick updated');
            return $this->redirectToRoute('app_homepage');
        }
        return $this->render('tricks/update.html.twig', [
            'updateTrickForm' => $updateTrick->createView(),
        ]);
    }
}