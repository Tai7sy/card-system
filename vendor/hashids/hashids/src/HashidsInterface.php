<?php
 namespace Hashids; interface HashidsInterface { public function encode(...$numbers); public function decode($hash); public function encodeHex($str); public function decodeHex($hash); } 