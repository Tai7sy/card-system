<?php
 namespace Predis\Command; class PubSubUnsubscribeByPattern extends PubSubUnsubscribe { public function getId() { return 'PUNSUBSCRIBE'; } } 