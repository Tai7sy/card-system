<?php
 namespace Predis\Command; class ListPopLastBlocking extends ListPopFirstBlocking { public function getId() { return 'BRPOP'; } } 