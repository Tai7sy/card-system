<?php
 namespace Illuminate\Auth\Events; use Illuminate\Queue\SerializesModels; class Logout { use SerializesModels; public $user; public function __construct($user) { $this->user = $user; } } 