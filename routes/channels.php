<?php
Broadcast::channel('App.User.{id}', function ($sp766bcc, $spb3d6c6) { return (int) $sp766bcc->id === (int) $spb3d6c6; });