<?php
 namespace Predis\Command; class ListPopLastPushHeadBlocking extends Command { public function getId() { return 'BRPOPLPUSH'; } } 