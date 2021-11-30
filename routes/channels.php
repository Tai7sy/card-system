<?php
Broadcast::channel('App.User.{id}', function ($sp21e2d0, $sp0cebcc) { return (int) $sp21e2d0->id === (int) $sp0cebcc; });