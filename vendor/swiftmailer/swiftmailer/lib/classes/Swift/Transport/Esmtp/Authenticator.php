<?php
 interface Swift_Transport_Esmtp_Authenticator { public function getAuthKeyword(); public function authenticate(Swift_Transport_SmtpAgent $agent, $username, $password); } 