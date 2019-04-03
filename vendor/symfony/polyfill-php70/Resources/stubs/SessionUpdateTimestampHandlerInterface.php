<?php
 interface SessionUpdateTimestampHandlerInterface { public function validateId($key); public function updateTimestamp($key, $val); } 