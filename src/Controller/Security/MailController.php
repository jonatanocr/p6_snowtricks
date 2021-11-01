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

    public function sendVerificationEmail($to, $url, $username)
    {
        $subject = 'Welcome to the SnowTricks community!';
        //$email = (new Email())
        $email = (new TemplatedEmail())
            ->from('mailer.ocr@gmail.com')
            ->to($to)
            ->subject($subject)
            ->htmlTemplate('emails/emailVerification.html.twig')
            ->context([
                'username' => ucfirst($username),
                'url' => $url,
            ]);
        $this->mailer->send($email);
    }
}