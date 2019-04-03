<?php
 namespace Symfony\Component\Routing\Tests\Fixtures\OtherAnnotatedClasses; trait AnonymousClassInTrait { public function test() { return new class() { public function foo() { } }; } } 