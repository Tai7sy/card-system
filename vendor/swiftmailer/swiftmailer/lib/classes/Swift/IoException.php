<?php
 class Swift_IoException extends Swift_SwiftException { public function __construct($message, $code = 0, Exception $previous = null) { parent::__construct($message, $code, $previous); } } 