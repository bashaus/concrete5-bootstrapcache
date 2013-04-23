<?php

class BootstrapCacheTest extends PHPUnit_Framework_TestCase
{

	function __construct()
	{
		// To help with factory autoloading
		BootstrapCache::$base_dir = realpath('../') . '/';
	}

	public function testFactory()
	{
		$this->assertEquals('BootstrapCache_Driver_Memcached', BootstrapCache::factory('Memcached'));
		$this->assertEquals('BootstrapCache_Driver_CacheLite', BootstrapCache::factory('CacheLite'));
	}
	
	public function testPageMatches()
	{
		$haystack = array(
			'login.*',
			'tools/.*',
			'dashboard/.*'
		);

		$this->assertTrue(BootstrapCache::page_matches('/index.php/login', $haystack));
		$this->assertTrue(BootstrapCache::page_matches('/login', $haystack));
		$this->assertTrue(BootstrapCache::page_matches('/login/one/two/three', $haystack));
		$this->assertTrue(BootstrapCache::page_matches('/index.php/login/one/two/three', $haystack));

		// should fail: no preceeding slash
		$this->assertFalse(BootstrapCache::page_matches('login', $haystack));

		// should fail: not in list
		$this->assertFalse(BootstrapCache::page_matches('/admin', $haystack));
	}
}