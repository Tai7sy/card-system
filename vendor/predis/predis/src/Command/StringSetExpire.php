<?php
 namespace Predis\Command; class StringSetExpire extends Command { public function getId() { return 'SETEX'; } } 