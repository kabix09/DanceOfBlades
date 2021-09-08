<?php
declare(strict_types=1);

namespace App\Service\Builder;

use App\Service\Token;
use App\Entity\{User, UserKeys};

class UserKeyBuilder extends GenericBuilder
{
    /**
     * @var UserKeys
     */
    private UserKeys $object;

    public function __construct()
    {
        $this->newObject();
    }

    /**
     * @return $this
     */
    public function newObject(): self
    {
        $this->object = new UserKeys();

        return $this;
    }

    /**
     * @param \DateTime $createDate
     * @return $this
     */
    public function withCreateDate(\DateTime $createDate): self
    {
        $this->object->setCreateDate($createDate);

        return $this;
    }

    /**
     * @param \DateTime $expirationDate
     * @return $this
     */
    public function withExpirationDate(\DateTime $expirationDate): self
    {
        $this->object->setExpirationDate($expirationDate);

        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function withType(string $type): self
    {
        $this->object->setType($type);

        return $this;
    }

    /**
     * @param Token $token
     * @return $this
     */
    public function withToken(Token $token): self
    {
        $this->object->setValue(
            $token->getValue()
        );

        return $this;
    }

    /**
     * @param User $user
     * @return $this
     */
    public function withUser(User $user): self
    {
        $this->object->setUser($user);

        return $this;
    }

    /**
     * @return UserKeys
     */
    public function getObject(): UserKeys
    {
        return $this->object;
    }
}