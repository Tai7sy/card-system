<?php
 use voku\helper\Bootup; if (defined('PORTABLE_UTF8__DISABLE_AUTO_FILTER') === false) { Bootup::initAll(); Bootup::filterRequestUri(); Bootup::filterRequestInputs(); } 