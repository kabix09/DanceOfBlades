<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EventParticipantRepository;

/**
 * EventParticipant
 *
 * @ORM\Table(
 *     name="event_participant",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="PK_event_participant", columns={"event_id", "avatar_id"}),
 *          @ORM\UniqueConstraint(name="FK_event_participant_event", columns={"event_id"}),
 *          @ORM\UniqueConstraint(name="FK_event_participant_avatar", columns={"avatar_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass=EventParticipantRepository::class)
 */
class EventParticipant
{
    /**
     * @var EventsBook
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="EventsBook", inversedBy="avatar", cascade={"persist"})
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", nullable=false)
     */
    private EventsBook $event;

    /**
     * @var Avatar
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Avatar", mappedBy="id", inversedBy="event")
     * @ORM\JoinColumn(name="avatar_id", nullable=false)
     */
    private Avatar $avatar;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="join_member_date", type="mydatetime", nullable=false)
     */
    private DateTime $joinMemberDate;

    /**
     * @var int
     *
     * @ORM\Column(name="score", type="integer", nullable=false)
     */
    private int $score;

    public function __construct()
    {
        $this->joinMemberDate = new DateTime('now');
    }

    /**
     * @param EventsBook $event
     */
    public function setEvent(EventsBook $event): void
    {
        $this->event = $event;

        if($event->getAvatar() !== $this)
        {
            $event->addAvatar($this);
        }
    }

    /**
     * @return EventsBook
     */
    public function getEvent(): EventsBook
    {
        return $this->event;
    }

    /**
     * @return Avatar
     */
    public function getAvatar(): Avatar
    {
        return $this->avatar;
    }

    /**
     * @param Avatar $avatar
     */
    public function setAvatar(Avatar $avatar): self
    {
        $this->avatar = $avatar;

        if($avatar->getEvent() !== $this)
        {
            $avatar->setEvent($this);
        }

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getJoinMemberDate(): DateTime
    {
        return $this->joinMemberDate;
    }

    /**
     * @param DateTime $joinMemberDate
     */
    public function setJoinMemberDate(DateTime $joinMemberDate): self
    {
        $this->joinMemberDate = $joinMemberDate;

        return $this;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @param int $score
     */
    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }
}