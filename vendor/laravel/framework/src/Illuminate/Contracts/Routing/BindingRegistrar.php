<?php
 namespace Illuminate\Contracts\Routing; interface BindingRegistrar { public function bind($key, $binder); public function getBindingCallback($key); } 