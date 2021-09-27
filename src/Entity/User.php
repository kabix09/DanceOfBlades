<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
Use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(
 *     name="[user]",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="UNQ_user_email", columns={"email"}),
 *          @ORM\UniqueConstraint(name="IX_user_create_account_date", columns={"create_account_date"})},
 *     indexes={
 *          @ORM\Index(name="FK_session_user", columns={"id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     message="This email already exists"
 * )
 */
class User implements UserInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="guid", nullable=false, options={"default"="newid()"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private string $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=55, nullable=false)
     */
    private string $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private string $password;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json", length=255, nullable=false, options={"default"="['ROLE_USER']"})
     */
    private array $roles;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="last_login_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private DateTime $lastLoginDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="create_account_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private DateTime $createAccountDate;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="accept_terms_date", type="datetime", nullable=true)
     */
    private ?DateTime $acceptTermsDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=false)
     */
    private bool $isActive = false;

    /**
     * @ORM\OneToOne(targetEntity="Avatar", mappedBy="user", fetch="EXTRA_LAZY")
     */
    private $avatar;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->roles = ["ROLE_USER"];
        $this->lastLoginDate = new DateTime('now');
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getLastLoginDate(): ?\DateTimeInterface
    {
        return $this->lastLoginDate;
    }

    public function setLastLoginDate(\DateTimeInterface $lastLoginDate): self
    {
        $this->lastLoginDate = $lastLoginDate;

        return $this;
    }

    public function getCreateAccountDate(): ?\DateTimeInterface
    {
        return $this->createAccountDate;
    }

    public function setCreateAccountDate(\DateTimeInterface $createAccountDate): self
    {
        $this->createAccountDate = $createAccountDate;

        return $this;
    }

    public function getAcceptTermsDate(): ?\DateTimeInterface
    {
        return $this->acceptTermsDate;
    }

    public function setAcceptTermsDate(?\DateTimeInterface $acceptTermsDate): self
    {
        $this->acceptTermsDate = $acceptTermsDate;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getAvatar(): Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(Avatar $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }
}
