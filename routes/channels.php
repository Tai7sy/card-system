<?php
Broadcast::channel('App.User.{id}', function ($sp590011, $sp138835) { return (int) $sp590011->id === (int) $sp138835; });