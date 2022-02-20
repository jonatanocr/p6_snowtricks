<?php


namespace App\Controller\Tricks;


use App\Entity\Trick;
use App\Entity\TrickPicture;
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
        $trick = new Trick();
        $entityManager = $this->getDoctrine()->getManager();
        $trick = $entityManager->getRepository(Trick::class)->find($id);
        $updateTrick = $this->createForm(UpdateTrickFormType::class, $trick);
        $updateTrick->handleRequest($request);
        if ($updateTrick->isSubmitted() && $updateTrick->isValid()) {
            $trick->setLastUpdatedDate(new \DateTime());
            $entityManager->persist($trick);
            $entityManager->flush();
            $pictureFiles = $updateTrick->get('pictureFiles')->getData();
            foreach ($pictureFiles as $pictureFile) {
                if ($pictureFile) {
                    $trickPicture = New TrickPicture();
                    $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();
                    $destination = $this->getParameter('kernel.project_dir').'/public/uploads/tricks/'.$trick->getId();
                    try {
                        $pictureFile->move(
                            $destination,
                            $newFilename
                        );
                    } catch (FileException $e) {
                        echo "An error occurred while uploading your image";
                    }
                    $trickPicture->setTrick($trick);
                    $trickPicture->setPictureFile($pictureFile);
                    $trickPicture->setFilename($newFilename);
                    $entityManager->persist($trickPicture);
                }
            }
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