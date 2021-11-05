<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $random_hex = bin2hex(random_bytes(18));
            $user->setPassword(
            $userPasswordHasherInterface->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setVerified(0);
            $user->setHash($hash);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $url = $this->getUrl($user);
            $mailController = new MailController($mailer);
            $mailController->sendVerificationEmail($user->getEmail(), $url, $user->getUsername());
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/email_verification/{email}/{hash}", name="email_verification")
     */
    public function emailVerification($email, $hash): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user === null) {
            $this->addFlash('danger', 'Account not found');
        } else {
            if ($user->getVerified() == 1) {
                $this->addFlash('info', 'Your account is already verified');
            } else {
                if ($user->getHash() === $hash) {
                    $user->setVerified(1);
                    $entityManager->flush();
                    $this->addFlash('success', 'Your account is now verified and you can login');
                } else {
                    $this->addFlash('danger', 'An error occured');
                }
            }
        }
        return $this->redirectToRoute('app_homepage');
    }

    /**
     * @Route("/resend_email_verification/{email}", name="resend_email_verification")
     */
    public function resendEmailVerification($email, MailerInterface $mailer)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user === null) {
            $this->addFlash('danger', 'Account not found');
        } else {
            $url = $this->getUrl($user);
            $mailController = new MailController($mailer);
            $mailController->sendVerificationEmail($user->getEmail(), $url, $user->getUsername());
            $this->addFlash('success', 'An email with verification link was sent to your address');
        }
        return $this->redirectToRoute('app_homepage');
    }

    private function getUrl(User $user): string
    {
        $url = $this->generateUrl('email_verification', [
            'email' => $user->getUserIdentifier(),
            'hash' => $user->getHash()
        ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        return $url;
    }

    /**
     * @Route("/delete_user", name="delete_user")
     */
    public function deleteUser(Request $request, UserPasswordHasherInterface $userPasswordHasherInterface, MailerInterface $mailer): Response
    {
        $currentUserId = $this->getUser()->getId();
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($currentUserId);
        if ($user === null) {
            $this->addFlash('danger', 'An error occured ( ⚆ _ ⚆ )');
        } else {
            $session = new Session();
            $session->invalidate();
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_homepage');
    }

}
