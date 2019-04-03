<?php
 namespace Illuminate\Database\Migrations; abstract class Migration { protected $connection; public $withinTransaction = true; public function getConnection() { return $this->connection; } } 