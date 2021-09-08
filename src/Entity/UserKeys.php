<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
Use App\Repository\UserKeysRepository;

/**
 * UserKeys
 *
 * @ORM\Table(
 *     name="user_keys",
 *     indexes={
 *          @ORM\Index(name="IX_user_keys_user_uuid", columns={"user_id"})})
 * @ORM\Entity(repositoryClass=UserKeysRepository::class)
 */
class UserKeys
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
     * @ORM\Column(name="value", type="string", length=255, nullable=false)
     */
    private string $value;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=35, nullable=false)
     */
    private string $type;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="create_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private DateTime $createDate;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="expiration_date", type="datetime", nullable=false)
     */
    private DateTime $expirationDate;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private User $user;


    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->createDate = new DateTime('now');
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    public function setCreateDate(\DateTimeInterface $createDate): self
    {
        $this->createDate = $createDate;

        return $this;
    }

    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(\DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
