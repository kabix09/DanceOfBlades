<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Sluggable\Util\Urlizer;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;


/**
 * Item
 *
 * @ORM\Table(
 *     name="item",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="PK_item_uuid", columns={"id"}),
 *          @ORM\UniqueConstraint(name="CK_item_level", columns={"level"}),
 *          @ORM\UniqueConstraint(name="DF_item_level", columns={"level"}),
 *          @ORM\UniqueConstraint(name="DF_item_group", columns={"group"}),
 *          @ORM\UniqueConstraint(name="CK_item_required_user_level", columns={"required_user_level"})
 *     }
 * )
 * @ORM\Entity(repositoryClass=ItemRepository::class)
 */
class Item
{
    /**
     * @ORM\Column(name="id", type="uuid", nullable=false, options={"default"="newid()"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private string $id;

    /**
     * @ORM\Column(name="name", type="string", length=75, nullable=false)
     */
    private string  $name;

    /**
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private string $description;

    /**
     * @ORM\Column(name="level", type="integer", options={"default"="1"}, nullable=false)
     */
    private int $level;

    /**
     * @ORM\Column(name="type", type="string", length=30, nullable=false)
     */
    private string $type;

    /**
     * @ORM\Column(name="`group`", type="string", length=30, nullable=false)
     */
    private string $group;

    /**
     * @ORM\Column(name="value", type="smallint", nullable=false)
     */
    private int $value;

    /**
     * @ORM\Column(name="required_user_level", type="smallint", nullable=false)
     */
    private int $requiredUserLevel;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=120, nullable=true)
     */
    private string $image;

    /**
     * @var string
     */
    private string $slug = "";

    public function __construct()
    {
        $this->id = '';
        $this->name = '';
        $this->level = 1;
        $this->description = '';
        $this->group = '';
        $this->type = '';
        $this->value = 1;
        $this->requiredUserLevel = 1;
        $this->image = '';
        $this->slug = Urlizer::urlize($this->getName());
    }

    public function getId(): ?int
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

        $this->slug = Urlizer::urlize($this->getName());

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

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

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

    public function getGroup(): ?string
    {
        return $this->group;
    }

    public function setGroup(string $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getRequiredUserLevel(): ?int
    {
        return $this->requiredUserLevel;
    }

    public function setRequiredUserLevel(int $requiredUserLevel): self
    {
        $this->requiredUserLevel = $requiredUserLevel;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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
}
