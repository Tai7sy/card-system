<?php
 namespace Ramsey\Uuid\Generator; class SodiumRandomGenerator implements RandomGeneratorInterface { public function generate($length) { return \Sodium\randombytes_buf($length); } } 