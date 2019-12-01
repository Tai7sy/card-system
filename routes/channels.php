<?php
Broadcast::channel('App.User.{id}', function ($spb14cf0, $spbabe1d) { return (int) $spb14cf0->id === (int) $spbabe1d; });