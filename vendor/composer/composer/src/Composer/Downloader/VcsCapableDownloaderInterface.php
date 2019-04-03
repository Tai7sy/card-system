<?php
 namespace Composer\Downloader; use Composer\Package\PackageInterface; interface VcsCapableDownloaderInterface { public function getVcsReference(PackageInterface $package, $path); } 