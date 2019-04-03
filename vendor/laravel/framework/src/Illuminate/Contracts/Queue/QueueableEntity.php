<?php
 namespace Illuminate\Contracts\Queue; interface QueueableEntity { public function getQueueableId(); public function getQueueableConnection(); } 