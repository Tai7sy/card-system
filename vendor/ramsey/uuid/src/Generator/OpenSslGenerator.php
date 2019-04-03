<?php
 namespace Ramsey\Uuid\Generator; class OpenSslGenerator implements RandomGeneratorInterface { public function generate($length) { return openssl_random_pseudo_bytes($length); } } 