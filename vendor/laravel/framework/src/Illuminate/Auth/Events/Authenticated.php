<?php
 namespace Illuminate\Auth\Events; use Illuminate\Queue\SerializesModels; class Authenticated { use SerializesModels; public $user; public function __construct($user) { $this->user = $user; } } 