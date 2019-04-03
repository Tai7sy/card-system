<?php
 namespace Illuminate\Auth\Events; use Illuminate\Queue\SerializesModels; class PasswordReset { use SerializesModels; public $user; public function __construct($user) { $this->user = $user; } } 