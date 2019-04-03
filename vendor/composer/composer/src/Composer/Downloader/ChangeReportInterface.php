<?php
 namespace Composer\Downloader; use Composer\Package\PackageInterface; interface ChangeReportInterface { public function getLocalChanges(PackageInterface $package, $path); } 