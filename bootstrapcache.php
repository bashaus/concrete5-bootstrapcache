<?php defined('C5_EXECUTE') or die("Access Denied.");

class BootstrapCache {

	public $logger;
	public $driver;

	static $pages_exclude = array(
		'login.*',
		'tools/.*',
		'dashboard/.*'
	);

	static $pages_purge = array(
		'login/logout'
	);

	const DEFAULT_CACHE_TIME = 604800; // 60 * 60 * 24 * 7 -- 7 days

	function __construct() {
	}

	public static function factory($driver)
	{
		switch ($driver)
		{
			case 'memcached':
				require_once 'drivers/memcached.php';
				return 'BootstrapCache_Driver_Memcached';

			case 'cachelite':
				require_once 'drivers/cachelite.php';
				return 'BootstrapCache_Driver_CacheLite';
		}

		return null;
	}

	public function render($page=null) {
		// If no caching engine is specifed, skip
		if (is_null($this->driver)) {
			$this->log('debug', 'skipping cache as no engine is specified');
			return;
		}

		// If there was a request to clear the caching system, clear it
		if (isset($_GET['purgecache'])) {
			return $this->purge();
		}

		// Don't cache if the user is logged in
		if (isset($_SESSION['uID'])) {
			$this->log('debug', 'no caching as user is logged in');
			return;
		}

		// Don't use a cache if there is user data
		if (!empty($_GET)) {
			$this->log('debug', 'not caching as request contains GET params');
			return;
		}

		if (!empty($_POST)) {
			$this->log('debug', 'not caching as request contains POST params');
			return;
		}

		// Identify the page
		if (is_null($page)) {
			$page = $_SERVER['REQUEST_URI'];
		}

		// Clean the page
		$page = str_replace('/index.php', '', $page);
		$page = trim($page, '/');
		$page = '/' . $page;

		$this->log('debug', 'Caching for page: ' . $page);

		// If this is an excluded page, exclude it
		if (static::page_matches($page, static::$pages_exclude)) {
			$this->log('debug', 'skipped page, in exclusion list');
			return;
		}

		// If this is a purge page, purge it
		if (static::page_matches($page, static::$pages_purge)) {
			return $this->purge();
		}

		// Test if a cache is available and (if yes) return it to the browser. 
		// Else, the output buffering is activated.
		$this->driver->setKey($_SERVER['HTTP_HOST'], $page);
		$has_cached = $this->driver->start();
		if (!$has_cached) {
			$this->startup();
			return;
		}

		$this->log('debug', 'Outputting cached file');
		exit;
	}

	protected function startup() {
		$this->log('debug', 'No cached file, attaching shutdown function');
		return register_shutdown_function(array($this, 'shutdown'));
	}

	public function purge() {
		if (is_null($this->driver)) {
			return false;
		}

		$this->log('debug', 'purged cache');
		return $this->driver->purge();
	}
	
	/**
	 * Check to see if a page exists in a list
	 */

	protected static function page_matches($needle, $haystack) {
		foreach ($haystack as $straw) {
			if (preg_match('#^(\/index.php)?\/' . $straw . '$#i', $needle)) {
				return true;
			}
		}

		return false;
	}

	public function shutdown() {
		if (is_null($this->driver)) {
			return;
		}

		$this->driver->end();
		$this->log('debug', 'Cache ended');
	}	

	/* Logging */

	public function setLogger($logger) {
		$this->logger = $logger;
	}

	public function getLogger($logger) {
		return $this->logger;
	}

	protected function log($level, $message, array $context = array()) {
		if (is_null($this->logger)) {
			return;
		}

		$this->logger->log($level, '[BOOTSTRAPCACHE] ' . $message, $context);
	}

	/* Cache engine */

	public function setDriver($driver) {
		$this->driver = $driver;
	}

	public function getDriver($driver) {
		return $this->driver;
	}
}