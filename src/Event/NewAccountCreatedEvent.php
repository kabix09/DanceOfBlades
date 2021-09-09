<?php

namespace App\Event;

use App\Entity\User;
use App\Service\Token;
use Symfony\Contracts\EventDispatcher\Event;

class NewAccountCreatedEvent extends Event
{
    public const NAME = 'user.account.register';

    private User $user;
    private Token $token;

    /**
     * @param User $user
     * @param Token $token
     */
    public function __construct(User $user, Token $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getToken(): Token
    {
        return $this->token;
    }
}