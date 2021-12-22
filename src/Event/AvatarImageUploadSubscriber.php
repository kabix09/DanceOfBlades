<?php

namespace App\Event;

use App\Service\Uploader;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AvatarImageUploadSubscriber implements EventSubscriberInterface
{
    /**
     * @var Uploader
     */
    private Uploader $uploader;

    /**
     * @param Uploader $uploader
     */
    public function __construct(Uploader $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            AvatarImageUploadEvent::NAME => 'onAvatarImageUpload',
            AvatarImageDeleteEvent::NAME => 'onAvatarImageDelete'
        ];
    }

    public function onAvatarImageUpload(AvatarImageUploadEvent $event)
    {
        $this->uploader->uploadAvatarImage(
            $event->getFile(),
            $event->getImageName()
        );
    }

    public function onAvatarImageDelete(AvatarImageDeleteEvent $event)
    {
        $this->uploader->deleteAvatarImage(
            $event->getImageName()
        );
    }
}