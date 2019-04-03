<?php
 namespace Symfony\Component\Routing\Matcher; interface RedirectableUrlMatcherInterface { public function redirect($path, $route, $scheme = null); } 