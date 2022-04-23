<?php


namespace App\Controller\Tricks;


use App\Entity\TrickVideo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditTrickVideoController extends AbstractController
{
    /**
     * @Route("/trick_video_edit/{id}", name="trick_video_edit")
     */
    public function editVideo(Request $request, int $id): Response
    {
        $trickId = $request->get('trick_id');
        $trickVideo = $this->getDoctrine()
            ->getRepository(TrickVideo::class)
            ->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        if ($trickVideo === null) {
            $this->addFlash('danger', 'An error occured ( ⚆ _ ⚆ )');
        } else {
            $trickVideo->setUrl($request->get('trick_url'));
            $entityManager->flush();
            $this->addFlash('success', 'Video successfully edited');
        }
        return $this->redirectToRoute('trick_update', ['id' => $trickId]);
    }
}