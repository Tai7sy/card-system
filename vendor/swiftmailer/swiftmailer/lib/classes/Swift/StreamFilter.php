<?php
 interface Swift_StreamFilter { public function shouldBuffer($buffer); public function filter($buffer); } 