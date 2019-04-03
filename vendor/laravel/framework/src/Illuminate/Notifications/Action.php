<?php
 namespace Illuminate\Notifications; class Action { public $text; public $url; public function __construct($text, $url) { $this->url = $url; $this->text = $text; } } 