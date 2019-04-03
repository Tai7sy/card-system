<?php
 namespace Predis\Command; class KeyPreciseExpireAt extends KeyExpireAt { public function getId() { return 'PEXPIREAT'; } } 