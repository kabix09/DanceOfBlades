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
    /**
     * @var string
     */
    private string $mailAddress;
    /**
     * @var string
     */
    private string $mailOwner;

    public function __construct(MailerInterface $mailer, string $mailAddress, string $mailOwner)
    {
        $this->mailer = $mailer;
        $this->mailAddress = $mailAddress;
        $this->mailOwner = $mailOwner;
    }

    public function sendWelcomeMessage(User $user, Token $token): void
    {
        $registerEmail = (new TemplatedEmail())
            ->from(new Address($this->mailAddress, $this->mailOwner))
            ->to(new Address($user->getEmail(), explode("@", $user->getEmail())[0]))
            ->subject('Registration email')
            ->htmlTemplate('email/registerUser.html.twig')
            ->context([
                'activateToken' => $token->getValue()
            ]);

        $this->mailer->send($registerEmail);
    }
}