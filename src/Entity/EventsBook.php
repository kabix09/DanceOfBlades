<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use App\Repository\EventsBookRepository;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * EventsBook
 *
 * @ORM\Table(name="events_book", indexes={@ORM\Index(name="IX_rankings_book_name", columns={"name"})})
 * @ORM\Entity(repositoryClass=EventsBookRepository::class)
 */
class EventsBook
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
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
     * @ORM\Column(name="name", type="string", length=40, nullable=false)
     */
    private string $name;

    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(name="slug", type="string", length=40, nullable=false)
     */
    private string $slug;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=400, nullable=true)
     */
    private $description;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="start_event_date", type="mydatetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private DateTime $startEventDate;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="end_event_date", type="mydatetime", nullable=true)
     */
    private ?DateTime $endEventDate;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=25, nullable=true)
     */
    private $type;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="registration_opening_date", type="mydatetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private DateTime $registrationOpeningDate;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="smallint", nullable=false, options={"default"="1"})
     */
    private $level = '1';

    /**
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\EventBoss",
     *     mappedBy="event",
     *     indexBy="event",
     *     cascade={"persist"}
     * )
     */
    private Collection $boss;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\EventParticipant",
     *     mappedBy="event",
     *     cascade={"persist"}
     * )
     */
    private Collection $avatar;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="EventMap", inversedBy="event")
     * @ORM\JoinTable(name="event_map",
     *   joinColumns={
     *     @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="map_id", referencedColumnName="id")
     *   }
     * )
     */
    private Collection $map;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = "";
        $this->description = "";
        $this->level = 1;
        $this->registrationOpeningDate = new DateTime('now');
        $this->startEventDate = new DateTime('now');
        $this->endEventDate = null;
        $this->type = "";
        $this->boss = new \Doctrine\Common\Collections\ArrayCollection();
        $this->avatar = new \Doctrine\Common\Collections\ArrayCollection();
        $this->map = new \Doctrine\Common\Collections\ArrayCollection();
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

        return $this;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getRegistrationOpeningDate(): ?\DateTimeInterface
    {
        return $this->registrationOpeningDate;
    }

    public function setRegistrationOpeningDate(?\DateTimeInterface $registrationOpeningDate): self
    {
        $this->registrationOpeningDate = $registrationOpeningDate;

        return $this;
    }

    public function getStartEventDate(): \DateTimeInterface
    {
        return $this->startEventDate;
    }

    public function setStartEventDate(\DateTimeInterface $startEventDate): self
    {
        $this->startEventDate = $startEventDate;

        return $this;
    }

    public function getEndEventDate(): ?\DateTimeInterface
    {
        return $this->endEventDate;
    }

    public function setEndEventDate(?\DateTimeInterface $endEventDate): self
    {
        $this->endEventDate = $endEventDate;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|EventBoss[]
     */
    public function getBoss(): Collection
    {
        return $this->boss;
    }

    public function addBoss(EventBoss $boss): self
    {
        if(!$this->boss->contains($boss))
        {
            $this->boss[] = $boss;

            $boss->setEvent($this);
        }

        return $this;
    }

    public function removeBoss(EventBoss $boss): self
    {
        if ($this->boss->contains($boss)) {
            $this->boss->removeElement($boss);
            // set the owning side to null (unless already changed)
            if ($boss->getEvent() === $this) {
                $boss->setEvent(null);
            }
        }

        return $this;
    }

    public function setBoss($collection): self
    {
        if($collection instanceof Collection)
            $this->boss = $collection;
        else
            $this->boss->add($collection);

        return $this;
    }

    /**
     * @return Collection|EventParticipant[]
     */
    public function getAvatar(): Collection
    {
        return $this->avatar;
    }

    public function addAvatar(EventParticipant $avatar): self
    {
        if(!$this->avatar->contains($avatar))
        {
            $this->avatar[] = $avatar;
            $avatar->setEvent($this);
        }

        return $this;
    }

    public function removeAvatar(EventParticipant $avatar): self
    {
        if ($this->avatar->contains($avatar)) {
            $this->avatar->removeElement($avatar);
            // set the owning side to null (unless already changed)
            if ($avatar->getEvent() === $this) {
                $avatar->setEvent(null);
            }
        }

        return $this;
    }

    public function setAvatar(Collection $collection): self
    {
        $this->avatar = $collection;

        return $this;
    }

    /**
     * @return Collection|Map[]
     */
    public function getMap(): Collection
    {
        return $this->map;
    }

    public function addMap(Map $map): self
    {
        if(!$this->map->contains($map))
        {
            $this->map[] = $map;
        }

        return $this;
    }

    public function removeMap(Map $map): self
    {
        $this->map->remove($map);

        return $this;
    }

    public function setMap(Collection $collection): self
    {
        $this->avatar = $collection;

        return $this;
    }
}
