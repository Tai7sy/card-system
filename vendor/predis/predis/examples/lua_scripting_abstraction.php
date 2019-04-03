<?php
 require __DIR__.'/shared.php'; use Predis\Command\ScriptCommand; class IncrementExistingKeysBy extends ScriptCommand { public function getKeysCount() { return -1; } public function getScript() { return <<<LUA
local cmd, insert = redis.call, table.insert
local increment, results = ARGV[1], { }

for idx, key in ipairs(KEYS) do
  if cmd('exists', key) == 1 then
    insert(results, idx, cmd('incrby', key, increment))
  else
    insert(results, idx, false)
  end
end

return results
LUA;
} } $client = new Predis\Client($single_server, array( 'profile' => function ($options) { $profile = $options->getDefault('profile'); $profile->defineCommand('increxby', 'IncrementExistingKeysBy'); return $profile; }, )); $client->mset('foo', 10, 'foobar', 100); var_export($client->increxby('foo', 'foofoo', 'foobar', 50)); 