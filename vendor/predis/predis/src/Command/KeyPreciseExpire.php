<?php
 namespace Predis\Command; class KeyPreciseExpire extends KeyExpire { public function getId() { return 'PEXPIRE'; } } 