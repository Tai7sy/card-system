<?php
class WxPayException extends Exception { public function errorMessage() { return $this->getMessage(); } }