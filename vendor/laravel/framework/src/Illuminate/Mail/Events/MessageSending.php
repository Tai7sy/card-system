<?php
 namespace Illuminate\Mail\Events; class MessageSending { public $message; public function __construct($message) { $this->message = $message; } } 