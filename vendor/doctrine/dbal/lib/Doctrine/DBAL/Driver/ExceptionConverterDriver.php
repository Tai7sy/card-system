<?php
 namespace Doctrine\DBAL\Driver; interface ExceptionConverterDriver { public function convertException($message, DriverException $exception); } 