<?php
 namespace Predis\Command; class KeyPreciseTimeToLive extends KeyTimeToLive { public function getId() { return 'PTTL'; } } 