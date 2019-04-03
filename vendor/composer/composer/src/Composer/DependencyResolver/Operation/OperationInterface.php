<?php
 namespace Composer\DependencyResolver\Operation; interface OperationInterface { public function getJobType(); public function getReason(); public function __toString(); } 