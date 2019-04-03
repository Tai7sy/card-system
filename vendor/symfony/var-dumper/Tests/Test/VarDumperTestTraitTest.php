<?php
 namespace Symfony\Component\VarDumper\Tests\Test; use PHPUnit\Framework\TestCase; use Symfony\Component\VarDumper\Test\VarDumperTestTrait; class VarDumperTestTraitTest extends TestCase { use VarDumperTestTrait; public function testItComparesLargeData() { $howMany = 700; $data = array_fill_keys(range(0, $howMany), array('a', 'b', 'c', 'd')); $expected = sprintf("array:%d [\n", $howMany + 1); for ($i = 0; $i <= $howMany; ++$i) { $expected .= <<<EODUMP
  $i => array:4 [
    0 => "a"
    1 => "b"
    2 => "c"
    3 => "d"
  ]\n
EODUMP;
} $expected .= "]\n"; $this->assertDumpEquals($expected, $data); } } 