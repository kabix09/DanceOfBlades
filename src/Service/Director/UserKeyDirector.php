<?php
declare(strict_types=1);

namespace App\Service\Director;

use App\Service\Token;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\{User, UserKeys};
use App\Service\Builder\UserKeyBuilder;

class UserKeyDirector
{
    private UserKeyBuilder $builder;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager, UserKeyBuilder $userKeyBuilder)
    {
        $this->builder = $userKeyBuilder;
        $this->entityManager = $entityManager;
    }

    public function buildActivateAccount(Token $token, User $user)
    {
        $newAccountActivationKey = $this->builder
            ->newObject()
            ->withCreateDate(new \DateTime('now'))
            ->withExpirationDate((new \DateTime('now'))->modify('+5 day'))
            ->withType('ACTIVATE_ACCOUNT')
            ->withToken($token)
            ->withUser($user)
            ->getObject();

        $this->entityManager->persist($newAccountActivationKey);
        $this->entityManager->flush();
    }

    /* todo: build other type tokens */
}