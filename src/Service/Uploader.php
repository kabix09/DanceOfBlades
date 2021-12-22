<?php
declare(strict_types=1);

namespace App\Service;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;

class Uploader
{
    public const AVATAR_IMAGE = 'avatar_image';

    public const MAP_IMAGE = 'map_image';

    /**
     * @var Filesystem
     */
    private Filesystem $publicAvatarFilesystem;
    /**
     * @var Filesystem
     */
    private Filesystem $publicMapFilesystem;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Uploader constructor.
     * @param Filesystem $publicAvatarFilesystem
     * @param Filesystem $publicMapFilesystem
     * @param LoggerInterface $logger
     */
    public function __construct(Filesystem $publicAvatarFilesystem, Filesystem $publicMapFilesystem, LoggerInterface $logger)
    {
        $this->publicAvatarFilesystem = $publicAvatarFilesystem;
        $this->publicMapFilesystem = $publicMapFilesystem;

        $this->logger = $logger;
    }

    /**
     * @param File $file
     * @param string $newImageName
     */
    public function uploadAvatarImage(File $file, string $newImageName): void
    {
        try {
            /* open file descriptor */
            $imageStream = fopen($file->getPathname(), 'rb');

            $this->publicAvatarFilesystem->writeStream(
                self::AVATAR_IMAGE . '/' . $newImageName,
                $imageStream
            );

            if(is_resource($imageStream))
            {
                fclose($imageStream);
            }
        } catch (FilesystemException $e) {
            $this->logger->alert('Couldn\'t upload image');
        }
    }

    public function uploadMapImage(File $file, string $newImageName): void
    {
        try {
            /* open file descriptor */
            $imageStream = fopen($file->getPathname(), 'rb');

            $this->publicMapFilesystem->writeStream(
                self::MAP_IMAGE . '/' . $newImageName,
                $imageStream
            );

            if(is_resource($imageStream))
            {
                fclose($imageStream);
            }
        } catch (FilesystemException $e) {
            $this->logger->alert('Couldn\'t upload image');
        }
    }

    /**
     * @param string $imageName
     */
    public function deleteAvatarImage(string $imageName): void
    {
        try {
            $this->publicAvatarFilesystem->delete(self::AVATAR_IMAGE.'/'.$imageName);
        } catch (FilesystemException $e) {
            $this->logger->alert('Couldn\'t delete image: ');
        }
    }
}