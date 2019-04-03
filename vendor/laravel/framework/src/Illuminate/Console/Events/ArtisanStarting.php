<?php
 namespace Illuminate\Console\Events; class ArtisanStarting { public $artisan; public function __construct($artisan) { $this->artisan = $artisan; } } 