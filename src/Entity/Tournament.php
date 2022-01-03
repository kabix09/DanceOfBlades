<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tournament
 *
 * @ORM\Table(name="tournament", uniqueConstraints={@ORM\UniqueConstraint(name="UNQ_tournament", columns={"name", "user"})}, indexes={@ORM\Index(name="IDX_BD5FB8D95E237E06", columns={"name"}), @ORM\Index(name="IDX_BD5FB8D98D93D649", columns={"user"})})
 * @ORM\Entity
 */
class Tournament
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="guid", nullable=false, options={"default"="newid()"})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="join_member_date", type="datetime", nullable=true, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $joinMemberDate;

    /**
     * @var int|null
     *
     * @ORM\Column(name="score", type="integer", nullable=true)
     */
    private $score;

    /**
     * @var EventsBook
     *
     * @ORM\ManyToOne(targetEntity="EventsBook")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="name", referencedColumnName="id")
     * })
     */
    private $name;

    /**
     * @var Avatar|null
     *
     * @ORM\ManyToOne(targetEntity="Avatar")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="avatar", referencedColumnName="id")
     * })
     */
    private ?Avatar $avatar;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getJoinMemberDate(): ?\DateTimeInterface
    {
        return $this->joinMemberDate;
    }

    public function setJoinMemberDate(?\DateTimeInterface $joinMemberDate): self
    {
        $this->joinMemberDate = $joinMemberDate;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getName(): ?EventsBook
    {
        return $this->name;
    }

    public function setName(?EventsBook $name): self
    {
        $this->name = $name;

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
