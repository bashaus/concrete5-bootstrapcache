<?php

require_once 'drivers/DriverTest.php';

class CacheLiteDriverTest extends DriverTest
{
    
    function __construct()
    {
        // include required files
        $cachelite = BootstrapCache::factory('cachelite');

        // instantiate the driver
        $this->driver = new $cachelite(array(
            'caching'       => true,
            'cacheDir'      => '/tmp/',
            'lifeTime'      => 1, // 1 second
            'pearErrorMode' => CACHE_LITE_ERROR_RETURN
        ));
    }

    public function testDriverInstance()
    {
        $this->assertInstanceOf('BootstrapCache_Driver', $this->driver);
    }

    public function testDriverInstanceMemcached()
    {
        $this->assertInstanceOf('BootstrapCache_Driver_CacheLite', $this->driver);
    }

    public function testExpiration()
    {
        $this->driver->setKey(__CLASS__, __METHOD__);

        // Set expiration to 1 second
        $this->driver->set('0123456789');
        $this->assertEquals('0123456789', $this->driver->get());

        // Wait 2 seconds
        sleep(2);

        // Check the valid is gone
        $this->assertFalse($this->driver->get());
    }
}