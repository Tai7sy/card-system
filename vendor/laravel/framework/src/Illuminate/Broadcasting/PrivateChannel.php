<?php
 namespace Illuminate\Broadcasting; class PrivateChannel extends Channel { public function __construct($name) { parent::__construct('private-'.$name); } } 