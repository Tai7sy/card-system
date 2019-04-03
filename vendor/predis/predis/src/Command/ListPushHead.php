<?php
 namespace Predis\Command; class ListPushHead extends ListPushTail { public function getId() { return 'LPUSH'; } } 