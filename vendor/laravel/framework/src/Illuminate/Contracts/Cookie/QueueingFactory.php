<?php
 namespace Illuminate\Contracts\Cookie; interface QueueingFactory extends Factory { public function queue(...$parameters); public function unqueue($name); public function getQueuedCookies(); } 