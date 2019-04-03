<?php
 namespace Predis\Protocol; use Predis\Command\CommandInterface; interface RequestSerializerInterface { public function serialize(CommandInterface $command); } 