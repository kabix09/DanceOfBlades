<?php
declare(strict_types=1);

namespace App\Service\Director;

use App\Service\Token;
use App\Entity\{User, UserKeys};
use App\Service\Builder\UserKeyBuilder;

class UserKeyDirector
{
    private UserKeyBuilder $builder;

    public function __construct(UserKeyBuilder $userKeyBuilder)
    {
        $this->builder = $userKeyBuilder;
    }

    public function buildActivateAccount(Token $token, User $user): UserKeys
    {
        return $this->builder
            ->newObject()
            ->withCreateDate(new \DateTime('now'))
            ->withExpirationDate((new \DateTime('now'))->modify('+5 day'))
            ->withType('ACTIVATE_ACCOUNT')
            ->withToken($token)
            ->withUser($user)
            ->getObject();
    }

    /* todo: build other type tokens */
}