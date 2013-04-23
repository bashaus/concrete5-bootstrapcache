<?php

abstract class DriverTest extends PHPUnit_Framework_TestCase
{

	protected $driver;

    public function testRemove()
    {
        $this->driver->setKey(__CLASS__, __METHOD__);
        $this->driver->set('0123456789');
        $this->driver->remove();

        $this->assertFalse($this->driver->get());
    }

    public function testGetSet()
    {
        $this->driver->setKey(__CLASS__, __METHOD__);
        $this->driver->set('0123456789');
        $this->assertEquals('0123456789', $this->driver->get());
    }

    public function testEmpty()
    {
        $this->driver->setKey(__CLASS__, __METHOD__);
        $this->assertFalse($this->driver->get());
    }

    public function testKey()
    {
    	$this->driver->setKey(__CLASS__, __METHOD__);
		$this->assertEquals(__CLASS__, $this->driver->getGroup());
		$this->assertEquals(__METHOD__, $this->driver->getId());
	}
}