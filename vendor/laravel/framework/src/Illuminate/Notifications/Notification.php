<?php
 namespace Illuminate\Notifications; use Illuminate\Queue\SerializesModels; class Notification { use SerializesModels; public $id; public function broadcastOn() { return []; } } 