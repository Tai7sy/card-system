<?php
 namespace Predis\Command; class StringPreciseSetExpire extends StringSetExpire { public function getId() { return 'PSETEX'; } } 