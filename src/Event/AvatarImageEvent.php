<?php

namespace App\Event;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\EventDispatcher\Event;

class AvatarImageEvent extends Event
{
    public const NAME = 'avatar.image';

    /**
     * @var string
     */
    private string $imageName;

    /**
     * @param string $imageName
     */
    public function __construct(string $imageName)
    {
        $this->imageName = $imageName;
    }

    /**
     * @return string
     */
    public function getImageName(): string
    {
        return $this->imageName;
    }
}