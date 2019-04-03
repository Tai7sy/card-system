<?php
 namespace Doctrine\DBAL\Logging; interface SQLLogger { public function startQuery($sql, array $params = null, array $types = null); public function stopQuery(); } 