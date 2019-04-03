<?php
 namespace Illuminate\Contracts\Auth; interface Factory { public function guard($name = null); public function shouldUse($name); } 