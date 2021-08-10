<?php
declare(strict_types=1);

namespace App\Form\dto;

use App\Validator\UniqueUserEmail;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserModel
{
    /**
     * @Assert\NotBlank(message="Please enter a nick")
     * @Assert\Length(min=5, minMessage="Nick is too short")
     * @var string $nick
     */
    private $nick;
    /**
     * @Assert\NotBlank(message="Please enter an email")
     * @UniqueUserEmail()
     * @var string $email
     */
    private $email;

    /**
     * @Assert\NotBlank(message="Password is required")
     * @Assert\Length(min=8, maxMessage="This password is too short")
     * @var string $password
     */
    private $password;

    /**
     * @Assert\IsTrue(message="You must agree our terms")
     * @var bool $acceptTerms
     */
    private $acceptTerms;

    /**
     * @return string
     */
    public function getNick(): ?string
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
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return bool
     */
    public function isAcceptTerms(): ?bool
    {
        return $this->acceptTerms;
    }

    /**
     * @param bool $acceptTerms
     */
    public function setAcceptTerms(bool $acceptTerms): void
    {
        $this->acceptTerms = $acceptTerms;
    }
}