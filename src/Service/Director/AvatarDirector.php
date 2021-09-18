<?php
declare(strict_types=1);

namespace App\Service\Director;

use App\Entity\Avatar;
use App\Form\Dto\CreateAvatarModel;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Security;

class AvatarDirector
{
    /**
     * @var Security
     */
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;

        $this->avatar = new Avatar();
    }

    public function newAvatar(CreateAvatarModel $avatarModel): Avatar
    {
        $this->avatar
            ->setNick($avatarModel->getNick())
            ->setClass($avatarModel->getClass())
            ->setRace($avatarModel->getRace())
            ->setUser($this->security->getUser())
        ;

        /**
         * @var UploadedFile $image
         */
        $image = $avatarModel->getImage();
        if($image)
        {
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $newName = Urlizer::urlize($originalName) . '-' . uniqid('', true) . '.' . $image->guessExtension();

            $this->avatar->setImage($newName);
        }

        return $this->avatar;
    }
}