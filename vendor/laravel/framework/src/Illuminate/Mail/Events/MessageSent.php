<?php
 namespace Illuminate\Mail\Events; class MessageSent { public $message; public function __construct($message) { $this->message = $message; } } 