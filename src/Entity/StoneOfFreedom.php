<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * StoneOfFreedom
 *
 * @ORM\Table(name="stone_of_freedom", indexes={@ORM\Index(name="IX_stone_of_freedom_avatar", columns={"avatar_id"})})
 * @ORM\Entity
 */
class StoneOfFreedom
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
     * @ORM\Column(name="dungon", type="string", length=60, nullable=false)
     */
    private string $dungon;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="battle_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private DateTime $battleDate;

    /**
     * @var Avatar
     *
     * @ORM\ManyToOne(targetEntity="Avatar")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="avatar_id", referencedColumnName="id")
     * })
     */
    private Avatar $avatar;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getDungon(): ?string
    {
        return $this->dungon;
    }

    public function setDungon(string $dungon): self
    {
        $this->dungon = $dungon;

        return $this;
    }

    public function getBattleDate(): ?\DateTimeInterface
    {
        return $this->battleDate;
    }

    public function setBattleDate(\DateTimeInterface $battleDate): self
    {
        $this->battleDate = $battleDate;

        return $this;
    }

    public function getAvatar(): ?Avatar
    {
        return $this->avatar;
    }

    public function setAvatar(?Avatar $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }
}
