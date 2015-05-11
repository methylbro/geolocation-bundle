<?php

/**
* This file is part of the Meup GeoLocation Bundle.
*
* (c) 1001pharmacies <http://github.com/1001pharmacies/geolocation-bundle>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Meup\Bundle\GeoLocationBundle\Handler;

use Psr\Log\LoggerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Meup\Bundle\GeoLocationBundle\Model\LocationInterface;
use Meup\Bundle\GeoLocationBundle\Model\AddressInterface;
use Meup\Bundle\GeoLocationBundle\Model\CoordinatesInterface;
use Meup\Bundle\GeoLocationBundle\Model\LocationInterface;
use Meup\Bundle\GeoLocationBundle\Domain\BalancerFactoryInterface;

/**
 * 
 */
class LocatorManager implements LocatorManagerInterface
{
    /**
     * @var BalancerFactoryInterface
     */
    protected $balancerFactory;

    /**
     * @var LoggerInterface
     */
    protected $logger = null;

    /**
     * @var Array
     */
    protected $locators = array();

    public function __construct(
        BalancerFactoryInterface $balancerFactory,
        LoggerInterface $logger = null
    ) {
        $this->balancerFactory = $balancerFactory;
        $this->logger          = $logger;
    }

    /**
     * @var Psr\Cache\CacheItemPoolInterface
     */
    protected $cache;

    /**
     *
     */
    public function __construct(CacheItemPoolInterface $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function getLocators()
    {
        return $this->locators;
    }

    /**
     * {@inheritDoc}
     */
    public function addLocator(LocatorInterface $locator, array $attributes = array())
    {
        $this->locators[] = $locator;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function locate(LocationInterface $location, $force = false)
    {
        $result = $this->tryLocate($location);

        $this->log($location, $result);

        return $result;
    }

    private function tryLocate(LocationInterface $location)
    {
        $balancer = $this
            ->balancerFactory
            ->create($this->locators)
        ;

        $result = null;

        try {
            while(!$result) {
                $locator = $balancer->next();
                try {
                    $result = $locator->locate($location);
                } catch (\Exception $e) {

                }
            }
        } catch (\OutOfRangeException $e) {

        }

        return $result;
    }

    private function log($location, $result)
    {
        if ($this->logger) {
            if ($location instanceof AddressInterface) {
                $this
                    ->logger
                    ->debug(
                        'Geocoding : Find coordinates by address',
                        array(
                            'address'   => $location->getFullAddress(),
                            'latitude'  => $result->getLatitude(),
                            'longitude' => $result->getLongitude(),
                        )
                )
            ;
        }
    }
}
