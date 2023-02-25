<?php
Broadcast::channel('App.User.{id}', function ($sp586d7b, $spe8e527) { return (int) $sp586d7b->id === (int) $spe8e527; });