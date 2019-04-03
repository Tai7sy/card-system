<?php
 namespace Illuminate\Contracts\Auth; interface CanResetPassword { public function getEmailForPasswordReset(); public function sendPasswordResetNotification($token); } 