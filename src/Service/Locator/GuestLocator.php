<?php
declare(strict_types=1);

namespace App\Service\Locator;

use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\City;
use MaxMind\Db\Reader\InvalidDatabaseException;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\KernelInterface;

class GuestLocator
{
    /**
     * @var Request
     */
    private Request $request;
    /**
     * @var KernelInterface
     */
    private KernelInterface $kernel;

    private string $geoLiteDatabasePath;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(RequestStack $requestStack, LoggerInterface $logger, KernelInterface $kernel)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->logger = $logger;

        // set Geo2 mmdb file path
        $this->geoLiteDatabasePath = $kernel->getProjectDir() . '/private/GeoLite2-City_20211012/GeoLite2-City.mmdb';
    }

    public function getUserCity(): string
    {
        $userLocation = $this->getUserLocation();

        return is_null($userLocation) ? 'unknown' : $userLocation->city->name;
    }

    private function getUserLocation(): ?City
    {
        try{
            // Create an instance of the Reader of GeoIp2 and provide as first argument
            // the path to the database file
            $reader = new Reader($this->geoLiteDatabasePath);

            return $reader->city($this->request->getClientIp());

        } catch (AddressNotFoundException $ex) {
            // Couldn't retrieve geo information from the given IP
            $this->logger->error($ex->getMessage());
        } catch (InvalidDatabaseException $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }
}
