<?php
Broadcast::channel('App.User.{id}', function ($spe2c9ac, $speb3ceb) { return (int) $spe2c9ac->id === (int) $speb3ceb; });