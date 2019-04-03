<?php
 namespace Symfony\Component\VarDumper\Tests\Caster; use PHPUnit\Framework\TestCase; use Symfony\Component\VarDumper\Caster\PdoCaster; use Symfony\Component\VarDumper\Cloner\Stub; use Symfony\Component\VarDumper\Test\VarDumperTestTrait; class PdoCasterTest extends TestCase { use VarDumperTestTrait; public function testCastPdo() { $pdo = new \PDO('sqlite::memory:'); $pdo->setAttribute(\PDO::ATTR_STATEMENT_CLASS, array('PDOStatement', array($pdo))); $cast = PdoCaster::castPdo($pdo, array(), new Stub(), false); $this->assertInstanceOf('Symfony\Component\VarDumper\Caster\EnumStub', $cast["\0~\0attributes"]); $attr = $cast["\0~\0attributes"] = $cast["\0~\0attributes"]->value; $this->assertInstanceOf('Symfony\Component\VarDumper\Caster\ConstStub', $attr['CASE']); $this->assertSame('NATURAL', $attr['CASE']->class); $this->assertSame('BOTH', $attr['DEFAULT_FETCH_MODE']->class); $xDump = <<<'EODUMP'
array:2 [
  "\x00~\x00inTransaction" => false
  "\x00~\x00attributes" => array:9 [
    "CASE" => NATURAL
    "ERRMODE" => SILENT
    "PERSISTENT" => false
    "DRIVER_NAME" => "sqlite"
    "ORACLE_NULLS" => NATURAL
    "CLIENT_VERSION" => "%s"
    "SERVER_VERSION" => "%s"
    "STATEMENT_CLASS" => array:%d [
      0 => "PDOStatement"%A
    ]
    "DEFAULT_FETCH_MODE" => BOTH
  ]
]
EODUMP;
$this->assertDumpMatchesFormat($xDump, $cast); } } 