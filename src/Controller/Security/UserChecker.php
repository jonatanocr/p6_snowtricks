<?php


namespace App\Controller\Security;


use App\Entity\User as AppUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker extends AbstractController implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }

        if ($user->getVerified() != 1) {
            // the message passed to this exception is meant to be displayed to the user
            $url = $this->generateUrl('resend_email_verification', [
                'email' => $user->getUserIdentifier()
            ],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $full_url = '<a href="' . $url . '">Here</a>';
            $msg = 'Your account is not valid<br>Please check your email inbox<br>and click on the link to valid your email address<br><br>';
            $msg.= 'Click ' . $full_url . ' to receive a new validation email';
            $this->addFlash('danger', $msg);
            throw new CustomUserMessageAccountStatusException();
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if (!$user instanceof AppUser) {
            return;
        }
    }
}