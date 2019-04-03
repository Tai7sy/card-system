<?php
 namespace Symfony\Component\Translation\Formatter; interface MessageFormatterInterface { public function format($message, $locale, array $parameters = array()); } 