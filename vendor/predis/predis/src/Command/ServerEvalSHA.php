<?php
 namespace Predis\Command; class ServerEvalSHA extends ServerEval { public function getId() { return 'EVALSHA'; } public function getScriptHash() { return $this->getArgument(0); } } 