<?php
declare(strict_types=1);

namespace App\Form\Dto;

use App\Validator\UniqueUserEmail;
use Symfony\Component\Validator\Constraints as Assert;

class RegisterUserModel
{
    /**
     * @var string $email
     *
     * @Assert\NotBlank(message="Please enter an email")
     * @UniqueUserEmail()
     */
    private string $email;

    /**
     * @var string $password
     *
     * @Assert\NotBlank(message="Password is required")
     * @Assert\Length(min=8, maxMessage="This password is too short")
     */
    private string $password;

    /**
     * @var bool $acceptTerms
     *
     * @Assert\IsTrue(message="You must agree our terms")
     */
    private bool $acceptTerms;

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