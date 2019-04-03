<?php
 namespace Ramsey\Uuid\Generator; class RandomGeneratorFactory { public static function getGenerator() { return new RandomBytesGenerator(); } } 