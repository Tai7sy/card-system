<?php
 namespace Doctrine\Common\Cache; interface MultiPutCache { function saveMultiple(array $keysAndValues, $lifetime = 0); } 