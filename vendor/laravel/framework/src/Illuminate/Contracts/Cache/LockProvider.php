<?php
 namespace Illuminate\Contracts\Cache; interface LockProvider { public function lock($name, $seconds = 0); } 