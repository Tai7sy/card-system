<?php
 namespace Symfony\Component\Translation\Formatter; interface ChoiceMessageFormatterInterface { public function choiceFormat($message, $number, $locale, array $parameters = array()); } 