<?php
 namespace Illuminate\Contracts\Bus; interface QueueingDispatcher extends Dispatcher { public function dispatchToQueue($command); } 