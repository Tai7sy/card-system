<?php
 namespace Predis\Command; class KeyRandom extends Command { public function getId() { return 'RANDOMKEY'; } public function parseResponse($data) { return $data !== '' ? $data : null; } } 