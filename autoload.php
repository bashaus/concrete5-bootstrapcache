<?php

require_once 'drivers/driver.php';
require_once 'bootstrapcache_exception.php';
require_once 'bootstrapcache.php';

if (!defined('BOOTSTRAP_CACHE_PHPUNIT')) {
	require_once constant('DIR_CONFIG_SITE') . '/site_bootstrap_cache.php';
}