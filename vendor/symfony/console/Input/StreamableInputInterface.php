<?php
 namespace Symfony\Component\Console\Input; interface StreamableInputInterface extends InputInterface { public function setStream($stream); public function getStream(); } 