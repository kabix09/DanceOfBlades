<?php
declare(strict_types=1);

namespace App\Service\Director;

use App\Entity\Log;
use App\Entity\User;
use App\Service\Locator\GuestLocator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class LogDirector
{
    /**
     * @var Request
     */
    private Request $request;
    /**
     * @var GuestLocator
     */
    private GuestLocator $guestLocator;

    public function __construct(RequestStack $requestStack, GuestLocator $guestLocator)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->guestLocator = $guestLocator;
    }

    public function newDirectionLog(User $loginUser): Log
    {
        $location = $this->guestLocator->getUserCity();
        $deviceType = $this->isMobileDevice() ? 'mobile' : 'desktop';

        return (new Log())
            ->setUserIp($this->request->getClientIp())
            ->setUserBrowserData($this->request->server->get('HTTP_USER_AGENT'))
            ->setDeviceSystem($deviceType)
            ->setUserTown($location)
            ->setStartSessionDate(new \DateTime('now'))
            ->setUser($loginUser)
        ;
    }

    private function isMobileDevice() {
        return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i"
            , $this->request->server->get("HTTP_USER_AGENT"));
    }
}
