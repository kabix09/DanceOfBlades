<?php
declare(strict_types=1);

namespace App\Service\Builder;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserBuilder extends GenericBuilder
{
    /**
     * @var User
     */
    private User $object;
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->newObject();
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @return $this
     */
    public function newObject(): self
    {
        $this->object = new User();

        return $this;
    }

    /**
     * @param string $email
     * @return $this
     */
    public function withEmail(string $email): self
    {
        $this->object->setEmail($email);

        return $this;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function withPassword(string $password): self
    {
        $this->object->setPassword(
            $this->userPasswordEncoder->encodePassword(
                $this->object,
                $password
            )
        );

        return $this;
    }

    /**
     * @param string $role
     * @return $this
     */
    public function withRole(string $role): self
    {
        $roles = $this->object->getRoles();

        if(!in_array($role, $roles, true))
        {
            $this->object->setRoles(
                array_merge(
                    $roles,
                    $role
                )
            );
        }

        return $this;
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function withRoles(array $roles): self
    {
        foreach ($roles as $role)
        {
            $this->withRole($role);
        }

        return $this;
    }

    /**
     * @param \DateTime $lastLogin
     * @return $this
     */
    public function withLastLoginDate(\DateTime $lastLogin): self
    {
        $this->object->setLastLoginDate($lastLogin);

        return $this;
    }

    /**
     * @param \DateTime $createAccountDate
     * @return $this
     */
    public function withCreateAccountDate(\DateTime $createAccountDate): self
    {
        $this->object->setCreateAccountDate($createAccountDate);

        return $this;
    }

    /**
     * @param \DateTime $acceptTermsDate
     * @return $this
     */
    public function withAcceptTermsDate(\DateTime $acceptTermsDate): self
    {
        $this->object->setAcceptTermsDate($acceptTermsDate);

        return $this;
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function withIsActive(bool $flag): self
    {
        $this->object->setIsActive($flag);

        return $this;
    }

    /**
     * @return User
     */
    public function getObject(): User
    {
        return $this->object;
    }
}
