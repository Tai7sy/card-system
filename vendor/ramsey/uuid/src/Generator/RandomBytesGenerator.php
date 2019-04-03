<?php
 namespace Ramsey\Uuid\Generator; class RandomBytesGenerator implements RandomGeneratorInterface { public function generate($length) { return random_bytes($length); } } 