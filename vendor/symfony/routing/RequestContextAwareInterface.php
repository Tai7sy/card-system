<?php
 namespace Symfony\Component\Routing; interface RequestContextAwareInterface { public function setContext(RequestContext $context); public function getContext(); } 