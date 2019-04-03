<?php
 namespace Predis\Command; class ZSetReverseRangeByLex extends ZSetRangeByLex { public function getId() { return 'ZREVRANGEBYLEX'; } } 