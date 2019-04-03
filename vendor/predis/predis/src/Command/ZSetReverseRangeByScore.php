<?php
 namespace Predis\Command; class ZSetReverseRangeByScore extends ZSetRangeByScore { public function getId() { return 'ZREVRANGEBYSCORE'; } } 