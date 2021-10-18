<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Log
 *
 * @ORM\Table(name="log", indexes={@ORM\Index(name="FK_log_user_uuid", columns={"user_id"}), @ORM\Index(name="IX_log_session_sart_date", columns={"start_session_date"})})
 * @ORM\Entity
 */
class Log
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
     * @ORM\Column(name="user_ip", type="string", length=45, nullable=false)
     */
    private string $userIp;

    /**
     * @var string
     *
     * @ORM\Column(name="user_browser_data", type="string", length=255, nullable=false)
     */
    private string $userBrowserData;

    /**
     * @var string|null
     *
     * @ORM\Column(name="user_town", type="string", length=80, nullable=true)
     */
    private ?string $userTown;

    /**
     * @var string
     *
     * @ORM\Column(name="device_system", type="string", length=55, nullable=false)
     */
    private string $deviceSystem;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="start_session_date", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private DateTime $startSessionDate;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private User $user;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUserIp(): ?string
    {
        return $this->userIp;
    }

    public function setUserIp(string $userIp): self
    {
        $this->userIp = $userIp;

        return $this;
    }

    public function getUserBrowserData(): ?string
    {
        return $this->userBrowserData;
    }

    public function setUserBrowserData(string $userBrowserData): self
    {
        $this->userBrowserData = $userBrowserData;

        return $this;
    }

    public function getUserTown(): ?string
    {
        return $this->userTown;
    }

    public function setUserTown(?string $userTown): self
    {
        $this->userTown = $userTown;

        return $this;
    }

    public function getDeviceSystem(): ?string
    {
        return $this->deviceSystem;
    }

    public function setDeviceSystem(string $deviceSystem): self
    {
        $this->deviceSystem = $deviceSystem;

        return $this;
    }

    public function getStartSessionDate(): ?\DateTimeInterface
    {
        return $this->startSessionDate;
    }

    public function setStartSessionDate(\DateTimeInterface $startSessionDate): self
    {
        $this->startSessionDate = $startSessionDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }


}
