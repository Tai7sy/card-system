<?php
Broadcast::channel('App.User.{id}', function ($sp24cedd, $sp258ace) { return (int) $sp24cedd->id === (int) $sp258ace; });