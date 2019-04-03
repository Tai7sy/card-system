<?php
 $preferences = Swift_Preferences::getInstance(); $preferences->setCharset('utf-8'); if (@is_writable($tmpDir = sys_get_temp_dir())) { $preferences->setTempDir($tmpDir)->setCacheType('disk'); } 