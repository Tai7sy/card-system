<?php
 class Swift_TransportException extends Swift_IoException { public function __construct($message, $code = 0, Exception $previous = null) { parent::__construct($message, $code, $previous); } } 