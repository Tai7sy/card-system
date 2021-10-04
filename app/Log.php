<?php
 namespace App; use Illuminate\Database\Eloquent\Model; class Log extends Model { protected $guarded = []; const ACTION_LOGIN = 0; public $timestamps = false; } 