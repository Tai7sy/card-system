<?php
 namespace Predis\Command; class ListPopLastPushHead extends Command { public function getId() { return 'RPOPLPUSH'; } } 