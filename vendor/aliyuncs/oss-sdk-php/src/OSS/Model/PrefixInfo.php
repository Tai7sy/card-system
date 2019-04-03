<?php
 namespace OSS\Model; class PrefixInfo { public function __construct($prefix) { $this->prefix = $prefix; } public function getPrefix() { return $this->prefix; } private $prefix; }