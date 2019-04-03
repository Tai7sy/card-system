<?php
 namespace Psy\Test\Formatter; use Psy\Formatter\DocblockFormatter; class DocblockFormatterTest extends \PHPUnit\Framework\TestCase { private function methodWithDocblock($foo, $bar = 1) { if (empty($foo)) { throw new \InvalidArgumentException(); } return 'method called'; } public function testFormat() { $expected = <<<EOS
<comment>Description:</comment>
  This is a docblock!

<comment>Throws:</comment>
  <info>InvalidArgumentException </info> if \$foo is empty

<comment>Param:</comment>
  <info>mixed </info> <strong>\$foo </strong> It's a foo thing
  <info>int   </info> <strong>\$bar </strong> This is definitely bar

<comment>Return:</comment>
  <info>string </info> A string of no consequence

<comment>Author:</comment> Justin Hileman \<justin@justinhileman.info>
EOS;
$this->assertSame( $expected, DocblockFormatter::format(new \ReflectionMethod($this, 'methodWithDocblock')) ); } } 