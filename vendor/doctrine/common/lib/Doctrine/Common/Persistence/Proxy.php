<?php
 namespace Doctrine\Common\Persistence; interface Proxy { const MARKER = '__CG__'; const MARKER_LENGTH = 6; public function __load(); public function __isInitialized(); } 