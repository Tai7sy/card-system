<?php
Broadcast::channel('App.User.{id}', function ($spdc0e57, $sp1ffc0e) { return (int) $spdc0e57->id === (int) $sp1ffc0e; });