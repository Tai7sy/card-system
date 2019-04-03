<?php
 namespace Predis\Command; class SetDifferenceStore extends SetIntersectionStore { public function getId() { return 'SDIFFSTORE'; } } 