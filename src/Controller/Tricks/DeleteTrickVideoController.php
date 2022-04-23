<?php


namespace App\Controller\Tricks;


use App\Entity\TrickVideo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteTrickVideoController extends AbstractController
{
    /**
     * @Route("/trick_video_delete/{id}", name="trick_video_delete")
     */
    public function deleteVideo(Request $request, int $id): Response
    {
        $trickId = $request->get('trick_id');
        $trickVideo = $this->getDoctrine()
            ->getRepository(TrickVideo::class)
            ->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        if ($trickVideo === null) {
            $this->addFlash('danger', 'An error occured ( ⚆ _ ⚆ )');
        } else {
            $entityManager->remove($trickVideo);
            $entityManager->flush();
            $this->addFlash('danger', 'Video successfully deleted');
        }
        return $this->redirectToRoute('trick_update', ['id' => $trickId]);
    }
}