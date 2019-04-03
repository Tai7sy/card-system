<?php
 namespace Doctrine\DBAL\Driver; interface ServerInfoAwareConnection { public function getServerVersion(); public function requiresQueryForServerVersion(); } 