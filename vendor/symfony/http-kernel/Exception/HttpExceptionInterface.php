<?php
 namespace Symfony\Component\HttpKernel\Exception; interface HttpExceptionInterface { public function getStatusCode(); public function getHeaders(); } 