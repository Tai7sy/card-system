<?php
 namespace Predis\Response; interface ErrorInterface extends ResponseInterface { public function getMessage(); public function getErrorType(); } 