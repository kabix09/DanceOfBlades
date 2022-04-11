<?php

namespace App\MessageHandler;

use App\Message\SendRegisterEmail;
use App\Service\Mailer;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SendRegisterEmailHandler implements MessageHandlerInterface
{
    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(SendRegisterEmail $sendRegisterEmail)
    {
        /*
         * * email verification section - send email with link to unlock account
         */
        $this->mailer->sendWelcomeMessage(
            $sendRegisterEmail->getUser(),
            $sendRegisterEmail->getToken()
        );
    }
}