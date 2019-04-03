<?php
 namespace Illuminate\Contracts\Mail; interface MailQueue { public function queue($view, $queue = null); public function later($delay, $view, $queue = null); } 