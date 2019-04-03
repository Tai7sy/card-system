<?php
 namespace Illuminate\Notifications\Messages; class DatabaseMessage { public $data = []; public function __construct(array $data = []) { $this->data = $data; } } 