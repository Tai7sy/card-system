<?php
 interface Swift_Events_ResponseListener extends Swift_Events_EventListener { public function responseReceived(Swift_Events_ResponseEvent $evt); } 