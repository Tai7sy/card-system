<?php
 namespace Symfony\Component\Translation\Loader; class PhpFileLoader extends FileLoader { protected function loadResource($resource) { return require $resource; } } 