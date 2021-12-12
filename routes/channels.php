<?php
Broadcast::channel('App.User.{id}', function ($sp3db17d, $spd5afc6) { return (int) $sp3db17d->id === (int) $spd5afc6; });