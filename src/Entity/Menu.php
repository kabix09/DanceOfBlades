<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menu
 *
 * @ORM\Table(name="menu", indexes={@ORM\Index(name="FK_menu_parent_uuid", columns={"parent_id"})})
 * @ORM\Entity
 */
class Menu
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
     * @ORM\Column(name="category", type="string", length=30, nullable=false)
     */
    private string $category;

    /**
     * @var int
     *
     * @ORM\Column(name="hierarchy", type="smallint", nullable=false)
     */
    private int $hierarchy;

    /**
     * @var int
     *
     * @ORM\Column(name="sequency", type="smallint", nullable=false)
     */
    private int $sequency;

    /**
     * @var Menu|null
     *
     * @ORM\ManyToOne(targetEntity="Menu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    private ?Menu $parent;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getHierarchy(): ?int
    {
        return $this->hierarchy;
    }

    public function setHierarchy(int $hierarchy): self
    {
        $this->hierarchy = $hierarchy;

        return $this;
    }

    public function getSequency(): ?int
    {
        return $this->sequency;
    }

    public function setSequency(int $sequency): self
    {
        $this->sequency = $sequency;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}
