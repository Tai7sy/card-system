<?php
Broadcast::channel('App.User.{id}', function ($sp264a55, $sp39113c) { return (int) $sp264a55->id === (int) $sp39113c; });