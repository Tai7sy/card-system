<?php
 interface Swift_Filterable { public function addFilter(Swift_StreamFilter $filter, $key); public function removeFilter($key); } 