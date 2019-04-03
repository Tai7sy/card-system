<?php
 namespace Predis\Command; class KeyRenamePreserve extends KeyRename { public function getId() { return 'RENAMENX'; } } 