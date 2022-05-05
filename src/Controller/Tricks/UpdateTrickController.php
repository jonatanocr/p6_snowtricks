<?php


namespace App\Controller\Tricks;


use App\Entity\Trick;
use App\Entity\TrickPicture;
use App\Entity\TrickVideo;
use App\Form\Tricks\TrickVideoType;
use App\Form\Tricks\UpdateTrickFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
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
        $this->denyAccessUnlessGranted('ROLE_USER');
        $entityManager = $this->getDoctrine()->getManager();
        $trick = new Trick();
        $trick = $entityManager->getRepository(Trick::class)->find($id);
        $updateTrick = $this->createForm(UpdateTrickFormType::class, $trick);
        $updateTrick->handleRequest($request);
        if ($updateTrick->isSubmitted() && $updateTrick->isValid()) {
            $destination = $this->getParameter('kernel.project_dir').'/public/uploads/tricks/'.$trick->getId();
            $mainPictureFile = $updateTrick->get('mainPicture')->getData();
            if ($mainPictureFile) {
                $filename = $trick->getMainPicture();
                if (!empty($filename)) {
                    $filesystem = new Filesystem();
                    try {
                        $trickPicturePath = $this->getParameter('kernel.project_dir').'/public/uploads/tricks/'.$id.'/'.$filename;
                        $filesystem->remove($trickPicturePath);
                    } catch (IOExceptionInterface $exception) {
                        $this->addFlash("danger", "An error occurred while deleting picture");
                    }
                }
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
            $trick->setLastUpdatedDate(new \DateTime());
            $entityManager->persist($trick);
            $entityManager->flush();
            $pictureFiles = $updateTrick->get('pictureFiles')->getData();
            $trickUpload = New UploadPicturesController();
            $trickUpload->uploadPictures($trick, $pictureFiles, $slugger, $destination, $entityManager);
            $entityManager->flush();
            $this->addFlash('success', 'Trick updated');
            return $this->redirectToRoute('trick_display', ['id' => $id]);
        }
        return $this->render('tricks/update.html.twig', [
            'updateTrickForm' => $updateTrick->createView(),
            'trick' => $trick
        ]);
    }

    /**
     * @Route("/trick_video_add/{id}", name="trick_video_add")
     */
    public function addVideo(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $trick = $entityManager->getRepository(Trick::class)->find($id);
        $videoCounter = 0;
        $videoCounter = $request->get('input_counter');
        for ($i = 1; $i <= $videoCounter; $i++) {
            $trickVideo = new TrickVideo();
            $trickVideo->setUrl($request->get('url_' . $i));
            $trickVideo->setTrick($trick);
            $entityManager->persist($trickVideo);
        }
        $entityManager->flush();
        return $this->redirectToRoute('trick_display', ['id' => $id]);
    }
}