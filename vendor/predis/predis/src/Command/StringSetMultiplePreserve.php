<?php
 namespace Predis\Command; class StringSetMultiplePreserve extends StringSetMultiple { public function getId() { return 'MSETNX'; } } 