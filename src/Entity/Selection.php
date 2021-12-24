<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * Selection
 *
 * @ORM\Table(
 *     name="selection",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="CK_selection_type", columns={"type"}),
 *          @ORM\UniqueConstraint(name="UC_selection_name", columns={"name", "type"}),
 *          @ORM\UniqueConstraint(name="DF_selection_uuid", columns={"id"}),
 *          @ORM\UniqueConstraint(name="DF_selection_create_date", columns={"selection_create"})
 *     },
 *     indexes={
 *          @ORM\Index(name="IDX_96A50CD7CAF85815", columns={"dependency_tag"}),
 *          @ORM\Index(name="CK_selection_type", columns={"type"})
 *     }
 * )
 * @ORM\Entity
 */
class Selection
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
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private string $name;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=60, nullable=false)
     */
    private string $type;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="creation_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private DateTime $creationDate;

    /**
     * @var Selection
     *
     * @ORM\ManyToOne(targetEntity="Selection")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dependency_tag", referencedColumnName="id")
     * })
     */
    private Selection $dependencyTag;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->creationDate = new DateTime('now');
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getDependencyTag(): ?self
    {
        return $this->dependencyTag;
    }

    public function setDependencyTag(?self $dependencyTag): self
    {
        $this->dependencyTag = $dependencyTag;

        return $this;
    }

}
