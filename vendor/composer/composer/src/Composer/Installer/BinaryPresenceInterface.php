<?php
 namespace Composer\Installer; use Composer\Package\PackageInterface; interface BinaryPresenceInterface { public function ensureBinariesPresence(PackageInterface $package); } 