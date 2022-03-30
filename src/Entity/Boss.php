<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Sluggable\Util\Urlizer;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * Boss
 *
 * @ORM\Table(
 *     name="boss",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="PK_boss_uuid", columns={"id"}),
 *          @ORM\UniqueConstraint(name="DF_boss_uuid", columns={"id"}),
 *          @ORM\UniqueConstraint(name="CK_boss_defence", columns={"defence"}),
 *          @ORM\UniqueConstraint(name="PK_boss_health", columns={"health"}),
 *          @ORM\UniqueConstraint(name="PK_boss_magic", columns={"magic"}),
 *          @ORM\UniqueConstraint(name="PK_boss_speed", columns={"speed"}),
 *          @ORM\UniqueConstraint(name="PK_boss_strength", columns={"strength"})
 *     }
 * )
 * @ORM\Entity
 */
class Boss
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="uuid", nullable=false, options={"default"="newid()"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private string $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private string $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="level", type="integer", nullable=false, options={"default"="1"})
     */
    private int $level = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="strength", type="integer", nullable=false, options={"default"="7"})
     */
    private $strength;

    /**
     * @var integer
     *
     * @ORM\Column(name="defence", type="integer", nullable=false, options={"default"="5"})
     */
    private $defence;

    /**
     * @var integer
     *
     * @ORM\Column(name="health", type="integer", nullable=false, options={"default"="150"})
     */
    private $health;

    /**
     * @var integer
     *
     * @ORM\Column(name="magic", type="integer", nullable=false, options={"default"="0"})
     */
    private $magic;

    /**
     * @var integer
     *
     * @ORM\Column(name="speed", type="integer", nullable=false, options={"default"="3"})
     */
    private $speed;

    /**
     * @var string
     *
     * @ORM\Column(name="race", type="string", nullable=false, length=50)
     */
    private $race;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", nullable=false, length=55)
     */
    private string $slug = "";

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(name="create_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createDate;

    /**
     * @var null | EventBoss
     *
     * @ORM\OneToOne(targetEntity="EventBoss", mappedBy="boss")
     */
    private ?EventBoss $event;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = '';
        $this->name = '';
        $this->description = "";
        $this->createDate = new DateTime('now');
        $this->slug = Urlizer::urlize($this->getName());            // regenerate slug after each name change
        $this->event = null;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        $this->slug = Urlizer::urlize($this->getName());            // regenerate slug after each name change

        return $this;
    }

    public function getSlug(): string
    {
        if(isset($this->slug) && !$this->slug) {
            $this->slug = Urlizer::urlize($this->getName());            // regenerate slug after each name change
        }

        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getStrength(): ?int
    {
        return $this->strength;
    }

    public function setStrength(int $strength): self
    {
        $this->strength = $strength;

        return $this;
    }

    public function getDefence(): ?int
    {
        return $this->defence;
    }

    public function setDefence(int $defence): self
    {
        $this->defence = $defence;

        return $this;
    }

    public function getHealth(): ?int
    {
        return $this->health;
    }

    public function setHealth(int $health): self
    {
        $this->health = $health;

        return $this;
    }

    public function getMagic(): ?int
    {
        return $this->magic;
    }

    public function setMagic(int $magic): self
    {
        $this->magic = $magic;

        return $this;
    }

    public function getSpeed(): ?string
    {
        return $this->speed;
    }

    public function setSpeed(string $speed): self
    {
        $this->speed = $speed;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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

    /**
     * @return null|EventBoss
     */
    public function getEvent(): ?EventBoss
    {
        return $this->event;
    }

    /**
     * @param EventBoss $event
     */
    public function setEvent(EventBoss $event): void
    {
        $this->event = $event;

        if($event->getBoss() !== $this)
        {
            $event->setBoss($this);
        }
    }

    public function __toString()
    {
        return $this->getName() ?? '';
    }
}
