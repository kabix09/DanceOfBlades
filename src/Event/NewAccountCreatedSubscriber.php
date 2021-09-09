<?php

namespace App\Event;

use App\Service\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NewAccountCreatedSubscriber implements EventSubscriberInterface
{
    private Mailer $mailer;

    /**
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NewAccountCreatedEvent::NAME => 'onAccountRegistered'
        ];
    }

    public function onAccountRegistered(NewAccountCreatedEvent $event)
    {
        /*
         * * email verification section
         */
        $this->mailer->sendWelcomeMessage($event->getUser(), $event->getToken());
    }
}