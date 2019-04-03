<?php
 namespace Predis\Command; class ServerEval extends Command { public function getId() { return 'EVAL'; } public function getScriptHash() { return sha1($this->getArgument(0)); } } 