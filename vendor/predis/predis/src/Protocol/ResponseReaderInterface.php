<?php
 namespace Predis\Protocol; use Predis\Connection\CompositeConnectionInterface; interface ResponseReaderInterface { public function read(CompositeConnectionInterface $connection); } 