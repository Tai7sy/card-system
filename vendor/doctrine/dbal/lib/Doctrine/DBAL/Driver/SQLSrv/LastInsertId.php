<?php
 namespace Doctrine\DBAL\Driver\SQLSrv; class LastInsertId { private $id; public function setId($id) { $this->id = $id; } public function getId() { return $this->id; } } 