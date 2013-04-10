<?php defined('C5_EXECUTE') or die("Access Denied.");

require_once __DIR__ . '/../cachelite/Lite.php';

class BootstrapCache_Driver_CacheLite extends BootstrapCache_Driver {
	
	protected $cacher;
	protected $id;
	protected $group;

	function __construct($options) {
		$this->cacher = new Cache_Lite($options);
	}

	// Cache functions

	public function get() {
		return $this->cacher->get($this->id, $this->group);
	}

	public function set($data) {
		return $this->cacher->save($data, $this->id, $this->group);
	}

	public function remove() {
		return $this->cacher->remove($this->id, $this->group);
	}

	public function purge() {
		$this->cacher->clean();
	}

	// Cache lite needs some special start methods

	public function start() {
		$response = parent::start();

		if (!$response) {
			return false;
		}

		// Check the cache size
		$cache_size = @filesize($this->cacher->_file);

		// if the following is true, the cache file is empty for an unknown reason
		// trigger a cache clean and generate the page
		// the following process is harmless, but needed to avoid some erratic behaviour
		if ($cache_size < 33) {
			// remove the empty cache file
			$this->remove();
			$this->log('debug', 'There was an error with the cache. Removing the key');
			return false;
		}

		return true;
	}
}