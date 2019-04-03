<?php
 namespace Doctrine\Tests\Common\Cache; use Doctrine\Common\Cache\XcacheCache; class XcacheCacheTest extends CacheTest { protected function _getCacheDriver() { return new XcacheCache(); } }