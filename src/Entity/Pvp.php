<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Pvp
 *
 * @ORM\Table(name="pvp", indexes={@ORM\Index(name="IX_pvp_first_player", columns={"first_player_id"}), @ORM\Index(name="IX_pvp_second_player", columns={"second_player_id"}), @ORM\Index(name="IX_pvp_start_battle_date", columns={"start_battle_date"}), @ORM\Index(name="IDX_82D01E0ECF6600E", columns={"winner"})})
 * @ORM\Entity
 */
class Pvp
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="guid", nullable=false, options={"default"="newid()"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id = 'newid()';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_battle_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $startBattleDate = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="duration_time", type="datetime", nullable=true)
     */
    private $durationTime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="place", type="string", length=36, nullable=true)
     */
    private $place;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="first_player_id", referencedColumnName="id")
     * })
     */
    private $firstPlayer;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="second_player_id", referencedColumnName="id")
     * })
     */
    private $secondPlayer;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="winner", referencedColumnName="id")
     * })
     */
    private $winner;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getStartBattleDate(): ?\DateTimeInterface
    {
        return $this->startBattleDate;
    }

    public function setStartBattleDate(\DateTimeInterface $startBattleDate): self
    {
        $this->startBattleDate = $startBattleDate;

        return $this;
    }

    public function getDurationTime(): ?\DateTimeInterface
    {
        return $this->durationTime;
    }

    public function setDurationTime(?\DateTimeInterface $durationTime): self
    {
        $this->durationTime = $durationTime;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(?string $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getFirstPlayer(): ?User
    {
        return $this->firstPlayer;
    }

    public function setFirstPlayer(?User $firstPlayer): self
    {
        $this->firstPlayer = $firstPlayer;

        return $this;
    }

    public function getSecondPlayer(): ?User
    {
        return $this->secondPlayer;
    }

    public function setSecondPlayer(?User $secondPlayer): self
    {
        $this->secondPlayer = $secondPlayer;

        return $this;
    }

    public function getWinner(): ?User
    {
        return $this->winner;
    }

    public function setWinner(?User $winner): self
    {
        $this->winner = $winner;

        return $this;
    }


}
