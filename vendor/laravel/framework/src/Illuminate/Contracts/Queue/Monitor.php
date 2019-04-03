<?php
 namespace Illuminate\Contracts\Queue; interface Monitor { public function looping($callback); public function failing($callback); public function stopping($callback); } 