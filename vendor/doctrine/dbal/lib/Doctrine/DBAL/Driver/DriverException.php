<?php
 namespace Doctrine\DBAL\Driver; interface DriverException { public function getErrorCode(); public function getMessage(); public function getSQLState(); } 