# Concrete5 Bootstrap Cache

Version 1.0.1

Concrete5 has excellent block caching which can increase the speed of your website; however there are instances where you don't need the power of a CMS to serve static pages. BootstrapCache caches content from Concrete 5 applications to speed up production servers. This project is based on Bootstrap Cache by Ryan Hewitt.

## Use case

You want your static pages to be served over cache because you don't need all the overhead of a CMS on every page load.

## Problem domain

This script extends the concepts that are discussed in the following article:

* mesuva.com.au - [An extra cache for Concrete5](http://www.mesuva.com.au/blog/technical-notes/an-extra-cache-for-concrete5/)
* kvz.io - [Speedup your website with Cache_Lite](http://kvz.io/blog/2007/08/01/speedup-your-website-with-cache-lite/)

## Compatibility

Tested on Concrete 5 version 5.6.0.2. Although it wil probably work on older versions too.

## Installation

Step 1. Update your concrete/despatcher file

Once you've opened the file, you will need to add the following line of code:

    ## Include the bootstrap cache
    require(DIR_LIBRARIES . '/concrete5-bootstrapcache/autoload.php');

You have two options where you add this line of code, both have advantages and disadvantages.

_Option 1:_ add below the following line (recommended):

    ## Check maintenance mode
    require(dirname(__FILE__) . '/startup/maintenance_mode_check.php');

Advantages: 

* More flexibile, reduces some issues with error pages (/page_not_found and /page_forbidden)

Disadvantages: 

* More database calls

_Option 2:_ add below the following line:

    ## Load session handlers
    require(dirname(__FILE__) . '/startup/session.php');

Advantages: 

* the cache is checked before the bulk of database calls have been made

Disadvantages: 

* can sometimes cause repetition issues with error pages (/page_not_found and /page_forbidden)

Step 2. Copy library files

Copy this repository to the libraries directory in your application.

Step 3. Setup configuration

Create the file and add your configuration (see the Configuration section below)

    config/site_bootstrap_cache.php

## Configuration

You must select a driver for how you would like to run your cache. Depending on your driver, you will need different dependencies. Currently supported drivers include:

* Memcached
* CacheLite

### Memcached

    <?php defined('C5_EXECUTE') or die("Access Denied.");
    /* config/site_bootstrap_cache.php */

    // connect to memcached server
    $memcached = new Memcached;
    $memcached->addServer('127.0.0.1', 11211);

    // instantiate a driver
    $driver_class = BootstrapCache::factory('memcached');
    $driver = new $driver_class($memcached);
    
    // run the bootstrap
    $bootstrap_cache = new BootstrapCache;
    $bootstrap_cache->setDriver($driver);
    $bootstrap_cache->attachEvents();
    $bootstrap_cache->render();

### CacheLite

    <?php defined('C5_EXECUTE') or die("Access Denied.");
    /* config/site_bootstrap_cache.php */
    
    // include required files
    $cachelite = BootstrapCache::factory('cachelite');

    // instantiate the driver
    $driver = new $cachelite(array(
        'caching'       => true,
        'cacheDir'      => '/tmp/',
        'lifeTime'      => BootstrapCache::DEFAULT_CACHE_TIME,
        'pearErrorMode' => CACHE_LITE_ERROR_RETURN
    ));
    
    // run the bootstrap
    $bootstrap_cache = new BootstrapCache;
    $bootstrap_cache->setDriver($driver);
    $bootstrap_cache->attachEvents();
    $bootstrap_cache->setKeyGroup('mysite');
    $bootstrap_cache->render();

## Usage

### Excluding pages from cache

By default, the following pages will never be cached:

* /login
* /tools/.*
* /dashboard/?.*
* /download_file/.*

You can add new paths to this list by editing a static variable. For example, if you never want to cache the page with the path /home you can add the following:

    BootstrapCache::$pages_exclude[] = 'home/?';

Notes for adding custom exclusions:

* Do not include the initial slash (/) in your URLs.
* Do not include /index.php (even if your site doesn't support friendly URLs)
* You can use regular expressions

You need to put this in your configuration file before you call the render() method.

### Automatic purging

You can force the cache to be purged when particular pages are loaded.

By default, the following pages will always purge the cache:

* login/logout

You can add new paths to this list by editing a static variable. For example, if you always want to purge the page with the path /home you can add the following:

    BootstrapCache::$pages_purge[] = 'home/?';

Notes for adding custom exclusions:

* Do not include the initial slash (/) in your URLs.
* Do not include /index.php (even if your site doesn't support friendly URLs)
* You can use regular expressions

You need to put this in your configuration file before you call the render() method.
You can also set the cache to be automatically purged when you make changes to the site. 
By using the following line of code in your configuration file:

    $bootstrap_cache->attachEvents();

The cache will automatically be purged when Concrete5 fires any of the following events:

* on_page_update
* on_page_move
* on_page_duplicate
* on_page_delete
* on_page_add
* on_page_version_approve

Attaching events is optional but recommended.

### Manually purging cache

If you want to clear your cache, simple add _purgecache_ to the query string in your URL.

E.g.: http://localhost/?purgecache

### Setting Key Groups

You can create separate groups for your cache by using the setKeyGroup method.

    $bootstrap_cache->setKeyGroup('mysite');

### Logging

You can use [Monolog](https://github.com/Seldaek/monolog) or any other PSR-3 compatible logging system to debug BootstrapCache. Here is an example of how to implement Monolog using a hybrid of monolog's example from their documentation:

    <?php defined('C5_EXECUTE') or die("Access Denied.");
    /* config/site_bootstrap_cache.php */

    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    
    // create a log channel
    $logger = new Logger('name');
    $logger->pushHandler(new StreamHandler('path/to/your.log'));
    
    $memcached = new Memcached;
    $memcached->addServer('127.0.0.1', 11211);

    $driver_class = BootstrapCache::factory('memcached');
    $driver = new $driver_class($memcached);
    
    $bootstrap_cache = new BootstrapCache;
    $bootstrap_cache->setDriver($driver);
    $bootstrap_cache->setLogger($logger);
    $bootstrap_cache->attachEvents();
    $bootstrap_cache->render();

### Dashboard

There are no dashboard configuration options for Bootstrap Cache.

## Licence

Copyright (C) 2013, [Bashkim Isai](http://www.bashkim.com.au)

This script is distributed under the MIT licence.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

## Original author

* @mesuva -- [Ryan Hewitt](http://www.mesuva.com.au/)

## Contributors

* @bashaus -- [Bashkim Isai](http://www.bashkim.com.au/)
* @bluefuton -- [Chris Rosser](http://www.bluefuton.com/)
* @avagraphique -- [avagraphique](https://github.com/avagraphique)

If you fork this project and create a pull request add your GitHub username, your full name and website to the end of list above.

## Changelog

### v1.0.1

Fixed page repetition issues identified in #1

### v1.0.0

Publically available