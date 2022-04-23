<?php


namespace App\Controller\Tricks;


use App\Entity\Trick;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class DeleteTrickController extends AbstractController
{
    /**
     * @Route("/trick_delete/{id}", name="trick_delete")
     */
    public function index(int $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $trick = $this->getDoctrine()
            ->getRepository(Trick::class)
            ->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        if ($trick === null) {
            $this->addFlash('danger', 'An error occured ( ⚆ _ ⚆ )');
            return $this->redirectToRoute('app_homepage');
        }
        $entityManager->remove($trick);
        $entityManager->flush();
        $filesystem = new Filesystem();
        try {
            $trickPictureFolder = $this->getParameter('kernel.project_dir').'/public/uploads/tricks/'.$id;
            $filesystem->remove($trickPictureFolder);
        } catch (IOExceptionInterface $exception) {
            $this->addFlash("danger", "An error occurred while deleting the trick");
        }
        $this->addFlash('danger', 'Trick successfully deleted');
        return $this->redirectToRoute('app_homepage');
    }
}