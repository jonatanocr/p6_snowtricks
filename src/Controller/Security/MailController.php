<?php


namespace App\Controller\Security;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailController
{
    public $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($recipient, $url, $username, $mailType)
    {
        switch ($mailType) {
            case "emailVerification":
                $subject = 'Welcome to the SnowTricks community!';
                $template = 'emails/emailVerification.html.twig';
                break;
            case "resetPassword":
                $subject = 'Snowtricks, Reset password instructions!';
                $template = 'emails/resetPasswordInstructions.html.twig';
                break;
        }
        $email = (new TemplatedEmail())
            ->from('mailer.ocr@gmail.com')
            ->to($recipient)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context([
                'username' => ucfirst($username),
                'url' => $url,
            ]);
        $this->mailer->send($email);
    }
}