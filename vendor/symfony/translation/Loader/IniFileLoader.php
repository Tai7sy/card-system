<?php
 namespace Symfony\Component\Translation\Loader; class IniFileLoader extends FileLoader { protected function loadResource($resource) { return parse_ini_file($resource, true); } } 