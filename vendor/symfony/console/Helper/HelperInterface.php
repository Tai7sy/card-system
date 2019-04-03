<?php
 namespace Symfony\Component\Console\Helper; interface HelperInterface { public function setHelperSet(HelperSet $helperSet = null); public function getHelperSet(); public function getName(); } 