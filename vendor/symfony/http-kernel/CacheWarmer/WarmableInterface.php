<?php
 namespace Symfony\Component\HttpKernel\CacheWarmer; interface WarmableInterface { public function warmUp($cacheDir); } 