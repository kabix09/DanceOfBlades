<?php
declare(strict_types=1);

namespace App\Form\Dto;

use App\Validator\CheckAvatarClass;
use App\Validator\CheckAvatarRace;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\UniqueAvatarNick;

class CreateAvatarModel
{
    /**
     * @var string
     *
     * @Assert\NotBlank(message="Please enter a nick")
     * @UniqueAvatarNick()
     */
    private string $nick;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Please choode a race")
     * @CheckAvatarRace()
     */
    private string $race;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="Please choose a class")
     * @CheckAvatarClass()
     */
    private string $class;

    /**
     * @var UploadedFile
     */
    private UploadedFile $image;

    /**
     * @return string
     */
    public function getNick(): string
    {
        return $this->nick;
    }

    /**
     * @param string $nick
     */
    public function setNick(string $nick): void
    {
        $this->nick = $nick;
    }

    /**
     * @return string
     */
    public function getRace(): string
    {
        return $this->race;
    }

    /**
     * @param string $race
     */
    public function setRace(string $race): void
    {
        $this->race = $race;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     */
    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    /**
     * @return UploadedFile
     */
    public function getImage(): UploadedFile
    {
        return $this->image;
    }

    /**
     * @param UploadedFile $image
     */
    public function setImage(UploadedFile $image): void
    {
        $this->image = $image;
    }
}