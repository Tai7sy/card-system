<?php
 namespace Illuminate\Redis\Limiters; use Illuminate\Contracts\Redis\LimiterTimeoutException; class ConcurrencyLimiter { protected $redis; protected $name; protected $maxLocks; protected $releaseAfter; public function __construct($redis, $name, $maxLocks, $releaseAfter) { $this->name = $name; $this->redis = $redis; $this->maxLocks = $maxLocks; $this->releaseAfter = $releaseAfter; } public function block($timeout, $callback = null) { $starting = time(); while (! $slot = $this->acquire()) { if (time() - $timeout >= $starting) { throw new LimiterTimeoutException; } usleep(250 * 1000); } if (is_callable($callback)) { return tap($callback(), function () use ($slot) { $this->release($slot); }); } return true; } protected function acquire() { $slots = array_map(function ($i) { return $this->name.$i; }, range(1, $this->maxLocks)); return $this->redis->eval($this->luaScript(), count($slots), ...array_merge($slots, [$this->name, $this->releaseAfter]) ); } protected function luaScript() { return <<<'LUA'
for index, value in pairs(redis.call('mget', unpack(KEYS))) do
    if not value then
        redis.call('set', ARGV[1]..index, "1", "EX", ARGV[2])
        return ARGV[1]..index
    end
end
LUA;
} protected function release($key) { $this->redis->del($key); } } 