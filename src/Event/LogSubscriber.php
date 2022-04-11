<?php

namespace App\Event;

use App\Service\Director\LogDirector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LogSubscriber implements EventSubscriberInterface
{

    /**
     * @var LogDirector
     */
    private LogDirector $logDirector;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     * @param LogDirector $logDirector
     */
    public function __construct(EntityManagerInterface $entityManager,LogDirector $logDirector)
    {
        $this->logDirector = $logDirector;
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            LogEvent::NAME => 'onLogUserLogin'
        ];
    }

    public function onLogUserLogin(LogEvent $event)
    {
        $logEntry = $this->logDirector->newLogEntry($event->getUser());

        $this->entityManager->persist($logEntry);
        $this->entityManager->flush();
    }
}