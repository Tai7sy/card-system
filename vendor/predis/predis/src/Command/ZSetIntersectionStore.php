<?php
 namespace Predis\Command; class ZSetIntersectionStore extends ZSetUnionStore { public function getId() { return 'ZINTERSTORE'; } } 