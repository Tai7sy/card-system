<?php
 namespace Illuminate\Contracts\Auth\Access; interface Authorizable { public function can($ability, $arguments = []); } 