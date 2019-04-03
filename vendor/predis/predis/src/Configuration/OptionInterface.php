<?php
 namespace Predis\Configuration; interface OptionInterface { public function filter(OptionsInterface $options, $value); public function getDefault(OptionsInterface $options); } 