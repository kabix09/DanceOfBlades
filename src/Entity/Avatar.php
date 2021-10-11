<?php

namespace App\Entity;

use App\Validator\CheckAvatarClass;
use App\Validator\CheckAvatarRace;
use App\Validator\UniqueAvatarNick;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
Use App\Repository\AvatarRepository;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Avatar
 *
 * @ORM\Table(
 *     name="avatar",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="UNQ_avatar_user_id", columns={"user_id"}),
 *          @ORM\UniqueConstraint(name="UNQ_IX_avatar_nickname", columns={"nickname"})},
 *     indexes={
 *          @ORM\Index(name="IX_avatar_nick", columns={"nick"}),
 *          @ORM\Index(name="IX_avatar_user_uuid", columns={"user_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass=AvatarRepository::class)
 */
class Avatar
{
    /**
     * @var string
     *
     * @Groups("avatar")
     * @ORM\Column(name="id", type="guid", nullable=false, options={"default"="newid()"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private string $id;

    /**
     * @var string
     *
     * @Groups("avatar")
     * @ORM\Column(name="nick", type="string", length=35, nullable=false)
     */
    private string $nick;

    /**
     * @var int
     *
     * @Groups("avatar")
     * @ORM\Column(name="level", type="smallint", nullable=false, options={"default"="1"})
     */
    private int $level;

    /**
     * @var string
     *
     * @Groups("avatar")
     * @ORM\Column(name="race", type="string", length=15, nullable=false)
     * @CheckAvatarRace()
     */
    private string $race;

    /**
     * @var string
     *
     * @Groups("avatar")
     * @ORM\Column(name="class", type="string", length=15, nullable=false)
     * @CheckAvatarClass()
     */
    private string $class;

    /**
     * @var string|null
     *
     * @Groups("avatar")
     * @ORM\Column(name="gift", type="string", length=25, nullable=true)
     */
    private ?string $gift;

    /**
     * @var string|null
     *
     * @Groups("avatar")
     * @ORM\Column(name="specialization", type="string", length=25, nullable=true)
     */
    private ?string $specialization;

    /**
     * @var string|null
     *
     * @Groups("avatar")
     * @ORM\Column(name="nickname", type="string", length=30, nullable=true)
     */
    private ?string $nickname;

    /**
     * @var int
     *
     * @Groups("avatar")
     * @ORM\Column(name="coins", type="integer", nullable=false, options={"default"="5000"})
     */
    private int $coins;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="avatar", fetch="EXTRA_LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private User $user;

    /**
     * @var string|null
     *
     * @Groups("avatar")
     * @ORM\Column(name="image", type="string", nullable=true)
     */
    private ?string $image;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->level = 1;
        $this->coins = 5000;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(string $nick): self
    {
        $this->nick = $nick;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getRace(): ?string
    {
        return $this->race;
    }

    public function setRace(string $race): self
    {
        $this->race = $race;

        return $this;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    public function getGift(): ?string
    {
        return $this->gift;
    }

    public function setGift(?string $gift): self
    {
        $this->gift = $gift;

        return $this;
    }

    public function getSpecialization(): ?string
    {
        return $this->specialization;
    }

    public function setSpecialization(?string $specialization): self
    {
        $this->specialization = $specialization;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getCoins(): ?int
    {
        return $this->coins;
    }

    public function setCoins(int $coins): self
    {
        $this->coins = $coins;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
