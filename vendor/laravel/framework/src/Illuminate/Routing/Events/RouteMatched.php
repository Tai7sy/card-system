<?php
 namespace Illuminate\Routing\Events; class RouteMatched { public $route; public $request; public function __construct($route, $request) { $this->route = $route; $this->request = $request; } } 