<?php defined('C5_EXECUTE') or die("Access Denied.");

class BootstrapCache_Driver_Memcached extends BootstrapCache_Driver {

	protected $cacher;

	protected $expiration = 604800; // 60 * 60 * 24 * 7 -- 7 days

	function __construct(Memcached $cacher)
	{
		$this->cacher = $cacher;
	}
	
	// Expiration

	public function setExpiration($expiration) {
		$this->expiration = $expiration;
	}
	
	public function getExpiration($expiration) {
		return $expiration;
	}

	// Cache functions

	public function remove() {
		return $this->cacher->delete($this->getKey());
	}

	public function get() {
		return $this->cacher->get($this->getKey());
	}

	public function set($data) {
		$this->cacher->set($this->getKey(), $data, $this->expiration);
	}

	public function purge() {
		$this->cacher->flush();
	}
}
