<?php
 namespace Composer\Downloader; use Composer\Package\PackageInterface; interface DvcsDownloaderInterface { public function getUnpushedChanges(PackageInterface $package, $path); } 