<?php


namespace App\Controller\Tricks;


use App\Entity\Trick;
use App\Entity\TrickPicture;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class UploadPicturesController extends AbstractController
{
    public function uploadPictures(Trick $trick, $pictureFiles, SluggerInterface $slugger, $destination, $entityManager)
    {
        foreach ($pictureFiles as $pictureFile) {
            if ($pictureFile) {
                $trickPicture = New TrickPicture();
                $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pictureFile->guessExtension();
                try {
                    $pictureFile->move(
                        $destination,
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash("danger", "An error occurred while uploading your image");
                }
                $trickPicture->setTrick($trick);
                $trickPicture->setPictureFile($pictureFile);
                $trickPicture->setFilename($newFilename);
                $entityManager->persist($trickPicture);
            }
        }
    }
}