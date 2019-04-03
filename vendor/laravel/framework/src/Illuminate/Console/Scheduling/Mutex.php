<?php
 namespace Illuminate\Console\Scheduling; interface Mutex { public function create(Event $event); public function exists(Event $event); public function forget(Event $event); } 