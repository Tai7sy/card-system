<?php
Broadcast::channel('App.User.{id}', function ($sp91cc0b, $spbf68a1) { return (int) $sp91cc0b->id === (int) $spbf68a1; });