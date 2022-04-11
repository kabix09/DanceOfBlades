<?php

namespace App\Event;

use App\Message\SendRegisterEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class NewAccountCreatedSubscriber implements EventSubscriberInterface
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
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
         * use messenger to dispatch sending create account email
         */
        $this->messageBus->dispatch(
            new SendRegisterEmail(
                $event->getUser(),
                $event->getToken()
            )
        );
    }
}
