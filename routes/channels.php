<?php
Broadcast::channel('App.User.{id}', function ($sp0a324a, $spaacfde) { return (int) $sp0a324a->id === (int) $spaacfde; });