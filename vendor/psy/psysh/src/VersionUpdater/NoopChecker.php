<?php
 namespace Psy\VersionUpdater; use Psy\Shell; class NoopChecker implements Checker { public function isLatest() { return true; } public function getLatest() { return Shell::VERSION; } } 