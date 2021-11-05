<?php

namespace App\Controller\Security;


use App\Entity\ChangePassword;
use App\Entity\User;
use App\Form\AccountInformationType;
use App\Form\PasswordUpdateFormType;
use App\Form\SettingsFormType;
use App\Service\Notification\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SettingsController extends AbstractController
{
    private $security;

    public function __construct(Security $security) {
        $this->security = $security;
    }

    /**
     * @Route("/settings", name="settings")
     */
    public function updateUser(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface): Response
    {
        $user = new User();
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($this->security->getUser()->getId());
        $formSettings = $this->createForm(SettingsFormType::class, $user);

        $changePassword = new ChangePassword();
        $formPassword = $this->createForm(PasswordUpdateFormType::class, $changePassword);

        $formSettings->handleRequest($request);

        if ($formSettings->isSubmitted() && $formSettings->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->redirectToRoute('settings');
        }

        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted() && $formPassword->isValid()) {
            $user->setPassword(
                $userPasswordHasherInterface->hashPassword(
                    $user,
                    $formPassword->get('newPassword')->getData()
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


            $entityManager->persist($user);
            $entityManager->flush();
            $this->redirectToRoute('settings');
        }

        return $this->render('settings/settings.html.twig', [
            'settingsForm' => $formSettings->createView(),
            'passwordForm' => $formPassword->createView(),
        ]);

    }
}