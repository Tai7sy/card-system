<?php
 interface Swift_Events_Event { public function getSource(); public function cancelBubble($cancel = true); public function bubbleCancelled(); } 