<?php

namespace App\Controller\Tricks;

use App\Entity\Trick;
use App\Form\Tricks\NewTrickFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class NewTrickController extends AbstractController
{
    /**
     * @Route("/newTrick", name="new_trick")
     */
    public function newTrick(Request $request, Security $security, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $trick = new Trick();
        $user = $security->getUser();
        $form = $this->createForm(NewTrickFormType::class, $trick);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $trick->setAuthor($user);
            $trick->setCreatedDate(new \DateTime());
            $videos = $form->get('trickVideos')->getData();
            foreach ($videos as $video) {
                $trick->addTrickVideo($video);
                $video->setTrick($trick);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();

            $destination = $this->getParameter('kernel.project_dir').'/public/uploads/tricks/'.$trick->getId();
            $trickUpload = New UploadPicturesController();
            $mainPictureFile = $form->get('mainPicture')->getData();
            if ($mainPictureFile) {
                $originalFilename = pathinfo($mainPictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$mainPictureFile->guessExtension();
                try {
                    $mainPictureFile->move(
                        $destination,
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash("danger", "An error occurred while uploading your image");
                }
                $trick->setMainPicture($newFilename);
            }
            $pictureFiles = $form->get('pictureFiles')->getData();
            $trickUpload->uploadPictures($trick, $pictureFiles, $slugger, $destination, $entityManager);
            $entityManager->flush();
            $this->addFlash('success', 'New trick is added');
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('tricks/new.html.twig', [
            'newTrickForm' => $form->createView(),
        ]);
    }
}