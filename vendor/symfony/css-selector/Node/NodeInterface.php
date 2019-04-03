<?php
 namespace Symfony\Component\CssSelector\Node; interface NodeInterface { public function getNodeName(); public function getSpecificity(); public function __toString(); } 