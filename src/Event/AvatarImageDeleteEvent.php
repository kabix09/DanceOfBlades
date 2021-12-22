<?php

namespace App\Event;

use Symfony\Component\HttpFoundation\File\File;

class AvatarImageDeleteEvent extends AvatarImageEvent
{
    public const NAME = parent::NAME . '.delete';

    public function __construct(string $imageName)
    {
        parent::__construct($imageName);
    }
}
