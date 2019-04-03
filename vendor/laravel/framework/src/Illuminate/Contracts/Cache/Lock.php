<?php
 namespace Illuminate\Contracts\Cache; interface Lock { public function get($callback = null); public function block($seconds, $callback = null); public function release(); } 