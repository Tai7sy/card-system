<?php
 namespace Illuminate\Queue\Connectors; use Illuminate\Queue\NullQueue; class NullConnector implements ConnectorInterface { public function connect(array $config) { return new NullQueue; } } 