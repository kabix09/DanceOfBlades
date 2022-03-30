<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\EventBossRepository;

/**
 * EventBoss
 *
 * @ORM\Table(
 *     name="event_boss",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="PK_raid_boss", columns={"event_id", "boss_id"}),
 *          @ORM\UniqueConstraint(name="FK_raid_boss_boss_uuid", columns={"boss_id"}),
 *          @ORM\UniqueConstraint(name="FK_raid_boss_raid_uuid", columns={"event_id"}),
 *          @ORM\UniqueConstraint(name="CK_raid_boss_diff_lvl", columns={"difficultness_level"})
 *     },
 *     indexes={
 *          @ORM\Index(name="IX_raid_boss_boss_uuid", columns={"boss_id"}),
 *          @ORM\Index(name="IX_raid_boss_event_uuid", columns={"event_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass=EventBossRepository::class)
 */
class EventBoss
{
    /**
     * @var EventsBook
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="EventsBook", inversedBy="boss", cascade={"persist"})
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", nullable=false)
     */
    private EventsBook $event;

    /**
     * @var Boss
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Boss", mappedBy="id", inversedBy="event")
     * @ORM\JoinColumn(name="boss_id", referencedColumnName="id", nullable=false)
     */
    private Boss $boss;

    /**
     * @var int
     *
     * @ORM\Column(name="difficultness_level", type="smallint", nullable=false, options={"default"="1"})
     */
    private int $difficultnessLevel;

    /**
     * @var int
     *
     * @ORM\Column(name="points", type="integer", nullable=false, options={"default"="0"})
     */
    private int $points;

    public function __construct()
    {
        $this->setBoss(new Boss());

        $this->difficultnessLevel = 1;
        $this->points = 0;
    }

    /**
     * @return EventsBook
     */
    public function getEvent(): EventsBook
    {
        return $this->event;
    }

    /**
     * @param EventsBook $event
     */
    public function setEvent(EventsBook $event): self
    {
        $this->event = $event;

        if(!$event->getBoss()->contains($this))
        {
            $event->addBoss($this);
        }

        return $this;
    }

    /**
     * @return Boss
     */
    public function getBoss(): Boss
    {
        return $this->boss;
    }

    /**
     * @param Boss $boss
     */
    public function setBoss(Boss $boss): self
    {
        $this->boss = $boss;

        if($boss->getEvent() !== $this)
        {
            $boss->setEvent($this);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getDifficultnessLevel(): int
    {
        return $this->difficultnessLevel;
    }

    /**
     * @param int $difficultnessLevel
     */
    public function setDifficultnessLevel(int $difficultnessLevel): void
    {
        $this->difficultnessLevel = $difficultnessLevel;
    }

    /**
     * @return int
     */
    public function getPoints(): int
    {
        return $this->points;
    }

    /**
     * @param int $points
     */
    public function setPoints(int $points): void
    {
        $this->points = $points;
    }
}