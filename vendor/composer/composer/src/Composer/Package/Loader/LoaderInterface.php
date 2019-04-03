<?php
 namespace Composer\Package\Loader; interface LoaderInterface { public function load(array $package, $class = 'Composer\Package\CompletePackage'); } 