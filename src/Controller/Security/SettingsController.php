<?php

namespace App\Controller\Security;


use App\Entity\ChangePassword;
use App\Entity\User;
use App\Form\Security\PasswordUpdateFormType;
use App\Form\Security\SettingsFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class SettingsController extends AbstractController
{
    private $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    /**
     * @Route("/settings", name="settings")
     */
    public function updateUser(Request $request, UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($this->security->getUser()->getId());
        $formSettings = $this->createForm(SettingsFormType::class, $user);
        $changePassword = new ChangePassword();
        $formPassword = $this->createForm(PasswordUpdateFormType::class, $changePassword);
        $formSettings->handleRequest($request);
        if ($formSettings->isSubmitted() && $formSettings->isValid()) {
            $avatarFile = $formSettings->get('avatar')->getData();
            if ($avatarFile) {
                $newFilename =  $user->getId().'.'.$avatarFile->guessExtension();
                $filesystem = new Filesystem();
                try {
                    $img_extensions = ['.png', '.jpg', '.jpeg'];
                    foreach ($img_extensions as $ext) {
                        if ($filesystem->exists($this->getParameter('users_directory').'/'.$user->getId().$ext)) {
                            $filesystem->remove($this->getParameter('users_directory').'/'.$user->getId().$ext);
                        }
                    }
                } catch (IOExceptionInterface $exception) {
                    $this->addFlash("danger", "An error occurred while uploading your image");
                }
                try {
                    $avatarFile->move(
                        $this->getParameter('users_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash("danger", "An error occurred while uploading your image");
                }
                $user->setAvatarFilename($newFilename);
            }
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Account updated');
            $this->redirectToRoute('settings');
        }
        $formPassword->handleRequest($request);
        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $formPassword->get('newPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Password updated');
            $this->redirectToRoute('settings');
        }
        return $this->render('settings/settings.html.twig', [
            'settingsForm' => $formSettings->createView(),
            'passwordForm' => $formPassword->createView(),
        ]);

    }
}