<?php
 namespace Illuminate\Contracts\Validation; interface Rule { public function passes($attribute, $value); public function message(); } 