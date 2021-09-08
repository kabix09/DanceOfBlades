<?php
declare(strict_types=1);

namespace App\Service\Director;

use App\Entity\User;
use App\Form\Dto\RegisterUserModel;
use App\Service\Builder\UserBuilder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserDirector
{
    /**
     * @var UserBuilder
     */
    private UserBuilder $builder;

    public function __construct(UserBuilder $userBuilder)
    {
        $this->builder = $userBuilder;
    }

    public function buildRegisterUser(RegisterUserModel $formObject): User
    {
        return $this->builder
            ->newObject()
            ->withEmail($formObject->getEmail())
            ->withPassword($formObject->getPassword())
            ->withAcceptTermsDate(new \DateTime("now"))
            ->withCreateAccountDate(new \DateTime("now"))
            ->withIsActive(false)
            ->getObject();
    }
}