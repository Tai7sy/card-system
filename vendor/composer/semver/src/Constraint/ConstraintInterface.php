<?php
 namespace Composer\Semver\Constraint; interface ConstraintInterface { public function matches(ConstraintInterface $provider); public function getPrettyString(); public function __toString(); } 