<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\Security\PasswordResetFormType;
use App\Form\Security\PasswordResetUserEmailFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/reset_password", name="reset_password")
     */
    public function emailForm(Request $request, MailerInterface $mailer) {
        $user = new User();
        $form = $this->createForm(PasswordResetUserEmailFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $form->get('email')->getData()]);
            if ($user === null) {
                $this->addFlash('danger', 'Account not found');
            } else {
                $url = $this->getUrl($user);
                $mailController = new MailController($mailer);
                $mailController->sendEmail($user->getEmail(), $url, $user->getUsername(), 'resetPassword');
                $this->addFlash('success', 'You will receive an email with instructions about how to reset your password in a few minutes.');
            }

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('login/resetPassword.html.twig', [
            'resetPasswordForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/confirm_reset_password/{email}/{hash}", name="confirm_reset_password")
     */
    public function newPasswordForm($email, $hash, Request $request, UserPasswordHasherInterface $userPasswordHasher) {
        $form = $this->createForm(PasswordResetFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = new User();
            $entityManager = $this->getDoctrine()->getManager();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user->getHash() == $hash) {
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('newPassword')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Password changed successfully.');
            } else {
                $this->addFlash('danger', 'Account error occured');
            }
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('login/confirmResetPassword.html.twig', [
            'resetPasswordForm' => $form->createView(),
        ]);
    }

    private function getUrl(User $user): string
    {
        $url = $this->generateUrl('confirm_reset_password', [
            'email' => $user->getUserIdentifier(),
            'hash' => $user->getHash()
        ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return $url;
    }
}