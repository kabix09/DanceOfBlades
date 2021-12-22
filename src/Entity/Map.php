<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Sluggable\Util\Urlizer;

/**
 * Map
 *
 * @ORM\Table(name="map", indexes={@ORM\Index(name="IX_map_name", columns={"name"}), @ORM\Index(name="IX_map_region_uuid", columns={"region"})})
 * @ORM\Entity
 */
class Map
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
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private string $name = "";

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private ?string $description = "";

    /**
     * @var string
     *
     * @ORM\Column(name="area_type", type="string", length=55, nullable=false)
     */
    private string $areaType = "";

    /**
     * @var string
     *
     * @ORM\Column(name="terrain_type", type="string", length=55, nullable=false)
     */
    private string $terrainType = "";

    /**
     * @var bool
     *
     * @ORM\Column(name="is_climate_influenced", type="boolean", nullable=false)
     */
    private bool $isClimateInfluenced = false;

    /**
     * @var string
     *
     * @ORM\Column(name="climate", type="string", length=55, nullable=false, options={"default"="Normal"})
     */
    private string $climate = 'Normal';

    /**
     * @var int
     *
     * @ORM\Column(name="dangerous_level", type="smallint", nullable=false, options={"default"="1"})
     */
    private int $dangerousLevel = 1;

    /**
     * @var bool
     *
     * @ORM\Column(name="no_battle_zone", type="boolean", nullable=false)
     */
    private bool $noBattleZone = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="no_violence_zone", type="boolean", nullable=false)
     */
    private bool $noViolenceZone = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="no_escape_zone", type="boolean", nullable=false)
     */
    private bool $noEscapeZone = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="no_magic_zone", type="boolean", nullable=false)
     */
    private bool $noMagicZone = false;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=80, nullable=false)
     */
    private string $image = "";

    /**
     * @var Map | null
     *
     * @ORM\ManyToOne(targetEntity="Map")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="region", referencedColumnName="id")
     * })
     */
    private ?Map $region;

    /**
     * @var string
     */
    private string $slug = "";

    public function __construct()
    {
        $this->slug = Urlizer::urlize($this->getName());            // regenerate slug after each name change
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAreaType(): ?string
    {
        return $this->areaType;
    }

    public function setAreaType(string $areaType): self
    {
        $this->areaType = $areaType;

        return $this;
    }

    public function getTerrainType(): ?string
    {
        return $this->terrainType;
    }

    public function setTerrainType(string $terrainType): self
    {
        $this->terrainType = $terrainType;

        return $this;
    }

    public function getClimate(): ?string
    {
        return $this->climate;
    }

    public function setClimate(string $climate): self
    {
        $this->climate = $climate;

        return $this;
    }

    public function getDangerousLevel(): ?int
    {
        return $this->dangerousLevel;
    }

    public function setDangerousLevel(int $dangerousLevel): self
    {
        $this->dangerousLevel = $dangerousLevel;

        return $this;
    }

    public function getNoBattleZone(): ?bool
    {
        return $this->noBattleZone;
    }

    public function setNoBattleZone(bool $noBattleZone): self
    {
        $this->noBattleZone = $noBattleZone;

        return $this;
    }

    public function getNoViolenceZone(): ?bool
    {
        return $this->noViolenceZone;
    }

    public function setNoViolenceZone(bool $noViolenceZone): self
    {
        $this->noViolenceZone = $noViolenceZone;

        return $this;
    }

    public function getNoEscapeZone(): ?bool
    {
        return $this->noEscapeZone;
    }

    public function setNoEscapeZone(bool $noEscapeZone): self
    {
        $this->noEscapeZone = $noEscapeZone;

        return $this;
    }

    public function getNoMagicZone(): ?bool
    {
        return $this->noMagicZone;
    }

    public function setNoMagicZone(bool $noMagicZone): self
    {
        $this->noMagicZone = $noMagicZone;

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

    public function getRegion(): ?self
    {
        return $this->region;
    }

    public function setRegion(?self $region): self
    {
        $this->region = $region;

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

    /**
     * @return bool
     */
    public function getIsClimateInfluenced(): bool
    {
        return $this->isClimateInfluenced;
    }

    /**
     * @param bool $isClimateInfluenced
     */
    public function setIsClimateInfluenced(bool $isClimateInfluenced): void
    {
        $this->isClimateInfluenced = $isClimateInfluenced;
    }
}

