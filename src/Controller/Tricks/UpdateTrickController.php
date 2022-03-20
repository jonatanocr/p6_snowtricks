<?php


namespace App\Controller\Tricks;


use App\Entity\Trick;
use App\Entity\TrickPicture;
use App\Entity\TrickVideo;
use App\Form\Tricks\TrickVideoType;
use App\Form\Tricks\UpdateTrickFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class UpdateTrickController extends AbstractController
{
    /**
     * @Route("/trick_update/{id}", name="trick_update")
     */
    public function updateTrick(Request $request, int $id, SluggerInterface $slugger): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $trick = new Trick();
        $trick = $entityManager->getRepository(Trick::class)->find($id);
        $updateTrick = $this->createForm(UpdateTrickFormType::class, $trick);
        $updateTrick->handleRequest($request);
        if ($updateTrick->isSubmitted() && $updateTrick->isValid()) {
            $trick->setLastUpdatedDate(new \DateTime());
            $entityManager->persist($trick);
            $entityManager->flush();
            $pictureFiles = $updateTrick->get('pictureFiles')->getData();
            $destination = $this->getParameter('kernel.project_dir').'/public/uploads/tricks/'.$trick->getId();
            $trickUpload = New UploadPicturesController();
            $trickUpload->uploadPictures($trick, $pictureFiles, $slugger, $destination, $entityManager);
            $entityManager->flush();
            $this->addFlash('success', 'Trick updated');
            return $this->redirectToRoute('app_homepage');
        }
        return $this->render('tricks/update.html.twig', [
            'updateTrickForm' => $updateTrick->createView(),
            'trick' => $trick
        ]);
    }
}