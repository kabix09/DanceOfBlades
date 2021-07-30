<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Menu
 *
 * @ORM\Table(name="menu", indexes={@ORM\Index(name="menu_primary_key_uuid", columns={"id"})})
 * @ORM\Entity
 */
class Menu
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=36, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="category", type="string", length=30, nullable=false)
     */
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=35, nullable=false)
     */
    private $slug;

    /**
     * @var int
     *
     * @ORM\Column(name="hierarchy", type="smallint", nullable=false)
     */
    private $hierarchy;

    /**
     * @var Menu
     *
     * @ORM\ManyToOne(targetEntity="Menu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    private $parent;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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
