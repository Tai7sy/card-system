<?php
 namespace Predis\Command; class ZSetReverseRange extends ZSetRange { public function getId() { return 'ZREVRANGE'; } } 