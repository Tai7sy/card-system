<?php
 class Swift_Events_TransportChangeEvent extends Swift_Events_EventObject { public function getTransport() { return $this->getSource(); } } 