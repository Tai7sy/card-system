<?php
Broadcast::channel('App.User.{id}', function ($sp845283, $sp746ee1) { return (int) $sp845283->id === (int) $sp746ee1; });