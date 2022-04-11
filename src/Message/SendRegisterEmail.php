<?php

namespace App\Message;

use App\Entity\User;
use App\Service\Token;

class SendRegisterEmail
{
    /**
     * @var User
     */
    private User $user;

    /**
     * @var Token
     */
    private Token $token;

    public function __construct(User $user, Token $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Token
     */
    public function getToken(): Token
    {
        return $this->token;
    }
}