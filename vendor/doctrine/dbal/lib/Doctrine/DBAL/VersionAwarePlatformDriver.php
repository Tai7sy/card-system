<?php
 namespace Doctrine\DBAL; interface VersionAwarePlatformDriver { public function createDatabasePlatformForVersion($version); } 