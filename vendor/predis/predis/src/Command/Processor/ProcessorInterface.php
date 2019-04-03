<?php
 namespace Predis\Command\Processor; use Predis\Command\CommandInterface; interface ProcessorInterface { public function process(CommandInterface $command); } 