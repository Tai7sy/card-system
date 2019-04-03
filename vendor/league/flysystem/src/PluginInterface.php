<?php
 namespace League\Flysystem; interface PluginInterface { public function getMethod(); public function setFilesystem(FilesystemInterface $filesystem); } 