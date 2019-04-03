<?php
 namespace Predis\Command; class PubSubSubscribeByPattern extends PubSubSubscribe { public function getId() { return 'PSUBSCRIBE'; } } 