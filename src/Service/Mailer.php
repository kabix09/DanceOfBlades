<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class Mailer
{
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendWelcomeMessage(User $user, Token $token): void
    {
        $registerEmail = (new TemplatedEmail())
            ->from(new Address('kabix.009@gmail.com', 'kabix009'))
            ->to(new Address($user->getEmail(), explode("@", $user->getEmail())[0]))
            ->subject('Registration email')
            ->htmlTemplate('email/registerUser.html.twig')
            ->context([
                'activateToken' => $token->getValue()
            ]);

        $this->mailer->send($registerEmail);
    }
}