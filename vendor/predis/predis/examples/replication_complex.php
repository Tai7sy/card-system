<?php
 require __DIR__.'/shared.php'; use Predis\Command\ScriptCommand; use Predis\Connection\Aggregate\MasterSlaveReplication; use Predis\Replication\ReplicationStrategy; class HashMultipleGetAll extends ScriptCommand { const BODY = <<<LUA
local hashes = {}
for _, key in pairs(KEYS) do
    table.insert(hashes, key)
    table.insert(hashes, redis.call('hgetall', key))
end
return hashes
LUA;
public function getScript() { return self::BODY; } } $parameters = array( 'tcp://127.0.0.1:6379/?alias=master', 'tcp://127.0.0.1:6380/?alias=slave', ); $options = array( 'profile' => function ($options, $option) { $profile = $options->getDefault($option); $profile->defineCommand('hmgetall', 'HashMultipleGetAll'); return $profile; }, 'replication' => function () { $strategy = new ReplicationStrategy(); $strategy->setScriptReadOnly(HashMultipleGetAll::BODY); $replication = new MasterSlaveReplication($strategy); return $replication; }, ); $client = new Predis\Client($parameters, $options); $hashes = $client->hmgetall('metavars', 'servers'); $replication = $client->getConnection(); $stillOnSlave = $replication->getCurrent() === $replication->getConnectionById('slave'); echo 'Is still on slave? ', $stillOnSlave ? 'YES!' : 'NO!', PHP_EOL; var_export($hashes); 