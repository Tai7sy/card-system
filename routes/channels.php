<?php
Broadcast::channel('App.User.{id}', function ($spac34b1, $sp8e8060) { return (int) $spac34b1->id === (int) $sp8e8060; });