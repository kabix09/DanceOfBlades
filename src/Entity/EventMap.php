<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class EventMap
 * @ORM\Table(
 *     name="event_map",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="PK_event_map", columns={"event_id", "map_id"}),
 *          @ORM\UniqueConstraint(name="FK_event_map_raid_uuid", columns={"boss_id"}),
 *          @ORM\UniqueConstraint(name="FK_event_map_map_uuid", columns={"map_id"})
 *     },
 *     indexes={
 *          @ORM\Index(name="IX_event_map_event_uuid", columns={"event_id"}),
 *          @ORM\Index(name="IX_event_map_map_uuid", columns={"map_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass=EventMapRepository::class)
 */
class EventMap
{
    /**
     * @var EventsBook
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="EventsBook", mappedBy="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private EventsBook $event;

    /**
     * @var Map
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Map", mappedBy="id")
     * @ORM\JoinColumn(nullable=false)
     */
    private Map $map;

    public function __construct()
    {
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
    public function setEvent(EventsBook $event): void
    {
        $this->event = $event;
    }

    /**
     * @return Map
     */
    public function getMap(): Map
    {
        return $this->map;
    }

    /**
     * @param Map $map
     */
    public function setMap(Map $map): void
    {
        $this->map = $map;
    }
}