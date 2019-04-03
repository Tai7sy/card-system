<?php
 namespace Illuminate\Auth\Events; use Illuminate\Http\Request; class Lockout { public $request; public function __construct(Request $request) { $this->request = $request; } } 