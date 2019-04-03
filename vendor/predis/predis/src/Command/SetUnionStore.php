<?php
 namespace Predis\Command; class SetUnionStore extends SetIntersectionStore { public function getId() { return 'SUNIONSTORE'; } } 