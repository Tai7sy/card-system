<?php
 namespace Illuminate\Foundation\Events; class LocaleUpdated { public $locale; public function __construct($locale) { $this->locale = $locale; } } 