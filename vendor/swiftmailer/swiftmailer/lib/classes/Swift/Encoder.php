<?php
 interface Swift_Encoder extends Swift_Mime_CharsetObserver { public function encodeString($string, $firstLineOffset = 0, $maxLineLength = 0); } 