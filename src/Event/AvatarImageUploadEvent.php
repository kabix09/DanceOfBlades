<?php

namespace App\Event;

use Symfony\Component\HttpFoundation\File\File;

class AvatarImageUploadEvent extends AvatarImageEvent
{
    public const NAME = parent::NAME . '.upload';

    /**
     * @var File
     */
    private File $file;

    /**
     * @param File $file
     * @param string $imageName
     */
    public function __construct(string $imageName, File $file)
    {
        parent::__construct($imageName);
        $this->file = $file;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->file;
    }
}