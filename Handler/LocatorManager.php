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

use Psr\Cache\CacheItemPoolInterface;
use Meup\Bundle\GeoLocationBundle\Model\LocationInterface;
use Meup\Bundle\GeoLocationBundle\Model\AddressInterface;
use Meup\Bundle\GeoLocationBundle\Model\CoordinatesInterface;

/**
 * 
 */
class LocatorManager implements LocatorManagerInterface
{
    /**
     * @var Array
     */
    protected $locators = array();

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
    public function addLocator(LocatorInterface $locator, Array $attributes = array())
    {
        $this->locators[] = $locator;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function locate(LocationInterface $location, $force = false)
    {
        $result = null;

        if ($this->cache) {

            $item = $this
                ->cache
                ->getItem(
                    $location instanceof AddressInterface 
                        ? $location->getFullAddress() 
                        : (
                            $location instanceof CoordinatesInterface 
                                ? sprintf('%s,%s',
                                    $location->getLatitude(),
                                    $location->getLongitude()
                                )
                                : null
                        )
                )
            ;

            if (!$force && $item->exists()) {
                $result = $item->get();
            }
        }

        if (!$result) {
            $key     = rand(0, count($this->locators)-1);
            $locator = $this->locators[$key];
            $result  = $locator->locate($location);

            if ($this->cache) {
                $this->cache->save($item->set($result));
            }
        }

        return $result;
    }
}
