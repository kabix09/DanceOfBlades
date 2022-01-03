<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * EventsBook
 *
 * @ORM\Table(name="events_book", indexes={@ORM\Index(name="IX_rankings_book_name", columns={"name"})})
 * @ORM\Entity
 */
class EventsBook
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
     * @ORM\Column(name="name", type="string", length=40, nullable=false)
     */
    private string $name;

    /**
     * @var string $slug
     *
     * @ORM\Column(name="slug", type="string", length=40, nullable=false)
     * @Gedmo\Slug(fields={"name"})
     */
    private string $slug;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=400, nullable=true)
     */
    private ?string $description;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="smallint", options={"default"="1"})
     */
    private int $level;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="registration_opening_date", type="mydatetime", nullable=true)
     */
    private ?DateTime $registrationOpeningDate;

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
    private ?string $type;

    public function __construct()
    {
        $this->name = "";
        $this->description = "";
        $this->level = 1;
        $this->registrationOpeningDate = null;
        $this->startEventDate = new DateTime('now');
        $this->endEventDate = null;
        $this->type = "";
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
}
