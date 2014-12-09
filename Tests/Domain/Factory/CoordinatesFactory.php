<?php

/**
* This file is part of the Meup GeoLocation Bundle.
*
* (c) 1001pharmacies <http://github.com/1001pharmacies/geolocation-bundle>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Meup\Bundle\GeoLocationBundle\Tests\Domain\Factory;

use Meup\Bundle\GeoLocationBundle\Domain\Factory\CoordinatesFactory;

/**
 *
 */
class CoordinatesFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * 
     */
    public function testCreate()
    {
        $class = 'Meup\Bundle\GeoLocationBundle\Domain\Model\Coordinates';
        $factory = new CoordinatesFactory($class);

        $this->assertInstanceOf($class, $factory->create());
    }

    /**
     * 
     */
    public function testCreateAssumingException()
    {
        $this->setExpectedException('InvalidArgumentException');
        $factory = new CoordinatesFactory('stdClass');
    }
}
