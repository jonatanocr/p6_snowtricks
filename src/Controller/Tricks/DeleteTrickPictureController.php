<?php

namespace App\Controller\Tricks;

use App\Entity\Trick;
use App\Entity\TrickPicture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeleteTrickPictureController extends AbstractController
{
    /**
     * @Route("/trick_picture_delete/{id}", name="trick_picture_delete")
     */
    public function index(Request $request, int $id): Response
    {
        $trickId = $request->get('trick_id');
        $trickPicture = $this->getDoctrine()
            ->getRepository(TrickPicture::class)
            ->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        if ($trickPicture === null) {
            $this->addFlash('danger', 'An error occured ( ⚆ _ ⚆ )');
        } else {
            $entityManager->remove($trickPicture);
            $entityManager->flush();
            $filename = $request->get('filename');
            $filesystem = new Filesystem();
            try {
                $trickPicturePath = $this->getParameter('kernel.project_dir').'/public/uploads/tricks/'.$trickId.'/'.$filename;
                $filesystem->remove($trickPicturePath);
            } catch (IOExceptionInterface $exception) {
                $this->addFlash("danger", "An error occurred while deleting picture");
            }
            $this->addFlash('danger', 'Picture successfully deleted');
        }
        return $this->redirectToRoute('trick_update', ['id' => $trickId]);
    }
}