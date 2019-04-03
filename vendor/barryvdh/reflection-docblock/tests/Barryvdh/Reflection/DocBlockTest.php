<?php
 namespace Barryvdh\Reflection; use Barryvdh\Reflection\DocBlock\Context; use Barryvdh\Reflection\DocBlock\Location; use Barryvdh\Reflection\DocBlock\Tag\ReturnTag; class DocBlockTest extends \PHPUnit_Framework_TestCase { public function testConstruct() { $fixture = <<<DOCBLOCK
/**
 * This is a short description
 *
 * This is a long description
 *
 * @see \MyClass
 * @return void
 */
DOCBLOCK;
$object = new DocBlock( $fixture, new Context('\MyNamespace', array('PHPDoc' => '\phpDocumentor')), new Location(2) ); $this->assertEquals( 'This is a short description', $object->getShortDescription() ); $this->assertEquals( 'This is a long description', $object->getLongDescription()->getContents() ); $this->assertCount(2, $object->getTags()); $this->assertTrue($object->hasTag('see')); $this->assertTrue($object->hasTag('return')); $this->assertFalse($object->hasTag('category')); $this->assertSame('MyNamespace', $object->getContext()->getNamespace()); $this->assertSame( array('PHPDoc' => '\phpDocumentor'), $object->getContext()->getNamespaceAliases() ); $this->assertSame(2, $object->getLocation()->getLineNumber()); } public function testConstructWithTagsOnly() { $fixture = <<<DOCBLOCK
/**
 * @see \MyClass
 * @return void
 */
DOCBLOCK;
$object = new DocBlock($fixture); $this->assertEquals('', $object->getShortDescription()); $this->assertEquals('', $object->getLongDescription()->getContents()); $this->assertCount(2, $object->getTags()); $this->assertTrue($object->hasTag('see')); $this->assertTrue($object->hasTag('return')); $this->assertFalse($object->hasTag('category')); } public function testIfStartOfTemplateIsDiscovered() { $fixture = <<<DOCBLOCK
/**#@+
 * @see \MyClass
 * @return void
 */
DOCBLOCK;
$object = new DocBlock($fixture); $this->assertEquals('', $object->getShortDescription()); $this->assertEquals('', $object->getLongDescription()->getContents()); $this->assertCount(2, $object->getTags()); $this->assertTrue($object->hasTag('see')); $this->assertTrue($object->hasTag('return')); $this->assertFalse($object->hasTag('category')); $this->assertTrue($object->isTemplateStart()); } public function testIfEndOfTemplateIsDiscovered() { $fixture = <<<DOCBLOCK
/**#@-*/
DOCBLOCK;
$object = new DocBlock($fixture); $this->assertEquals('', $object->getShortDescription()); $this->assertEquals('', $object->getLongDescription()->getContents()); $this->assertTrue($object->isTemplateEnd()); } public function testConstructOneLiner() { $fixture = '/** Short description and nothing more. */'; $object = new DocBlock($fixture); $this->assertEquals( 'Short description and nothing more.', $object->getShortDescription() ); $this->assertEquals('', $object->getLongDescription()->getContents()); $this->assertCount(0, $object->getTags()); } public function testConstructFromReflector() { $object = new DocBlock(new \ReflectionClass($this)); $this->assertEquals( 'Test class for Barryvdh\Reflection\DocBlock', $object->getShortDescription() ); $this->assertEquals('', $object->getLongDescription()->getContents()); $this->assertCount(4, $object->getTags()); $this->assertTrue($object->hasTag('author')); $this->assertTrue($object->hasTag('copyright')); $this->assertTrue($object->hasTag('license')); $this->assertTrue($object->hasTag('link')); $this->assertFalse($object->hasTag('category')); } public function testExceptionOnInvalidObject() { new DocBlock($this); } public function testDotSeperation() { $fixture = <<<DOCBLOCK
/**
 * This is a short description.
 * This is a long description.
 * This is a continuation of the long description.
 */
DOCBLOCK;
$object = new DocBlock($fixture); $this->assertEquals( 'This is a short description.', $object->getShortDescription() ); $this->assertEquals( "This is a long description.\nThis is a continuation of the long " ."description.", $object->getLongDescription()->getContents() ); } public function testInvalidTagBlock() { if (0 == ini_get('allow_url_include')) { $this->markTestSkipped('"data" URIs for includes are required.'); } include 'data:text/plain;base64,'. base64_encode( <<<DOCBLOCK_EXTENSION
<?php
class MyReflectionDocBlock extends \Barryvdh\Reflection\DocBlock {
    protected function splitDocBlock(\$comment) {
        return array('', '', 'Invalid tag block');
    }
}
DOCBLOCK_EXTENSION
); new \MyReflectionDocBlock(''); } public function testTagCaseSensitivity() { $fixture = <<<DOCBLOCK
/**
 * This is a short description.
 *
 * This is a long description.
 *
 * @method null something()
 * @Method({"GET", "POST"})
 */
DOCBLOCK;
$object = new DocBlock($fixture); $this->assertEquals( 'This is a short description.', $object->getShortDescription() ); $this->assertEquals( 'This is a long description.', $object->getLongDescription()->getContents() ); $tags = $object->getTags(); $this->assertCount(2, $tags); $this->assertTrue($object->hasTag('method')); $this->assertTrue($object->hasTag('Method')); $this->assertInstanceOf( __NAMESPACE__ . '\DocBlock\Tag\MethodTag', $tags[0] ); $this->assertInstanceOf( __NAMESPACE__ . '\DocBlock\Tag', $tags[1] ); $this->assertNotInstanceOf( __NAMESPACE__ . '\DocBlock\Tag\MethodTag', $tags[1] ); } public function testGetTagsByNameZeroAndOneMatch() { $object = new DocBlock(new \ReflectionClass($this)); $this->assertEmpty($object->getTagsByName('category')); $this->assertCount(1, $object->getTagsByName('author')); } public function testParseMultilineTag() { $fixture = <<<DOCBLOCK
/**
 * @return void Content on
 *     multiple lines.
 */
DOCBLOCK;
$object = new DocBlock($fixture); $this->assertCount(1, $object->getTags()); } public function testParseMultilineTagWithLineBreaks() { $fixture = <<<DOCBLOCK
/**
 * @return void Content on
 *     multiple lines.
 *
 *     One more, after the break.
 */
DOCBLOCK;
$object = new DocBlock($fixture); $this->assertCount(1, $tags = $object->getTags()); $tag = reset($tags); $this->assertEquals("Content on\n    multiple lines.\n\n    One more, after the break.", $tag->getDescription()); } public function testGetTagsByNameMultipleMatch() { $fixture = <<<DOCBLOCK
/**
 * @param string
 * @param int
 * @return void
 */
DOCBLOCK;
$object = new DocBlock($fixture); $this->assertEmpty($object->getTagsByName('category')); $this->assertCount(1, $object->getTagsByName('return')); $this->assertCount(2, $object->getTagsByName('param')); } } 