<?php

namespace App\Entity;

use App\Repository\FriendshipRepository;
use App\Types\MyDateTime;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Friendship
 *
 * @ORM\Table(
 *     name="friendship",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="FK_friendship_addressee", columns={"addressee_id"}),
 *          @ORM\UniqueConstraint(name="FK_friendship_requester", columns={"requester_id"})
 *     },
 *     indexes={
 *          @ORM\Index(name="FK_friendship_addressee", columns={"addressee_id"}),
 *          @ORM\Index(name="FK_friendship_requester", columns={"requester_id"})
 *     }
 * )
 * @ORM\Entity(repositoryClass=FriendshipRepository::class)
 */
class Friendship
{
    /**
     * @var Avatar
     *
     * @Groups("friendship")
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Avatar")
     * @ORM\JoinColumn(nullable=false)
     */
    private Avatar $requester;

    /**
     * @var Avatar
     *
     * @Groups("friendship")
     *
     * @ORM\Id
     *
     * @ORM\OneToOne(targetEntity="Avatar")
     * @ORM\JoinColumn(nullable=false)
     */
    private Avatar $addressee;

    /**
     * @var DateTime
     *
     * @Groups("friendship")
     *
     * @ORM\Id
     *
     * @ORM\Column(name="sent_date", type="mydatetime", nullable=false)
     */
    private DateTime $sentDate;

    /**
     * @var DateTime | null
     *
     * @Groups("friendship")
     * @ORM\Column(name="accepted_date", type="datetime", nullable=true)
     */
    private ?DateTime $acceptedDate;

    /**
     * @var DateTime | null
     *
     * @Groups("friendship")
     * @ORM\Column(name="rejected_date", type="datetime", nullable=true)
     */
    private ?DateTime $rejectedDate;

    /**
     * @var DateTime | null
     *
     * @Groups("friendship")
     * @ORM\Column(name="deleted_date", type="datetime", nullable=true)
     */
    private ?DateTime $deletedDate;

    public function __construct()
    {
        $this->acceptedDate = null;
        $this->rejectedDate = null;
        $this->deletedDate = null;
    }

    public function getRequester(): ?Avatar
    {
        return $this->requester;
    }

    public function setRequester(Avatar $requester): self
    {
        $this->requester = $requester;

        return $this;
    }

    public function getAddressee(): ?Avatar
    {
        return $this->addressee;
    }

    public function setAddressee(Avatar $addressee): self
    {
        $this->addressee = $addressee;

        return $this;
    }

    public function getSentDate(): \DateTimeInterface
    {
        return $this->sentDate;
    }

    public function setSentDate(\DateTimeInterface $sentDate): self
    {
        $this->sentDate = $sentDate;

        return $this;
    }

    public function getAcceptedDate(): ?\DateTimeInterface
    {
        return $this->acceptedDate;
    }

    public function setAcceptedDate(?\DateTimeInterface $acceptedDate): self
    {
        $this->acceptedDate = $acceptedDate;

        return $this;
    }

    public function getRejectedDate(): ?\DateTimeInterface
    {
        return $this->rejectedDate;
    }

    public function setRejectedDate(?\DateTimeInterface $rejectedDate): self
    {
        $this->rejectedDate = $rejectedDate;

        return $this;
    }

    public function getDeletedDate(): ?\DateTimeInterface
    {
        return $this->deletedDate;
    }

    public function setDeletedDate(?\DateTimeInterface $deletedDate): self
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }
}
