<?php

require_once 'drivers/DriverTest.php';

class MemcachedDriverTest extends DriverTest
{
    
    function __construct()
    {
        // connect to memcached server
        $memcached = new Memcached;
        $memcached->addServer('127.0.0.1', 11211);

        // instantiate a driver
        $driver_class = BootstrapCache::factory('memcached');
        $this->driver = new $driver_class($memcached);
    }

    public function testDriverInstance()
    {
        $this->assertInstanceOf('BootstrapCache_Driver', $this->driver);
    }

    public function testDriverInstanceMemcached()
    {
        $this->assertInstanceOf('BootstrapCache_Driver_Memcached', $this->driver);
    }

    public function testExpiration()
    {
        $this->driver->setKey(__CLASS__, __METHOD__);

        // Set expiration to 1 second
        $this->driver->setExpiration(1);
        $this->driver->set('0123456789');
        $this->assertEquals('0123456789', $this->driver->get());

        // Wait 2 seconds
        sleep(2);

        // Check the valid is gone
        $this->assertFalse($this->driver->get());
    }
}