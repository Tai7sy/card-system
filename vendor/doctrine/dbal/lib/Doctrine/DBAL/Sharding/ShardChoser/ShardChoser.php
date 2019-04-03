<?php
 namespace Doctrine\DBAL\Sharding\ShardChoser; use Doctrine\DBAL\Sharding\PoolingShardConnection; interface ShardChoser { function pickShard($distributionValue, PoolingShardConnection $conn); } 