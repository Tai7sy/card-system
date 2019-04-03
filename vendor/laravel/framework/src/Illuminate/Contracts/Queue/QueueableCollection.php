<?php
 namespace Illuminate\Contracts\Queue; interface QueueableCollection { public function getQueueableClass(); public function getQueueableIds(); public function getQueueableConnection(); } 