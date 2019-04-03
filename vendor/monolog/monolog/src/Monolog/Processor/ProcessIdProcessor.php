<?php
 namespace Monolog\Processor; class ProcessIdProcessor implements ProcessorInterface { public function __invoke(array $record) { $record['extra']['process_id'] = getmypid(); return $record; } } 