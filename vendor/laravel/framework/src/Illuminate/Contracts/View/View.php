<?php
 namespace Illuminate\Contracts\View; use Illuminate\Contracts\Support\Renderable; interface View extends Renderable { public function name(); public function with($key, $value = null); } 