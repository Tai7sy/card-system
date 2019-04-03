<?php
 pcntl_signal(SIGUSR1, function () { echo 'SIGUSR1'; exit; }); echo 'Caught '; $n = 0; while ($n++ < 400) { usleep(10000); pcntl_signal_dispatch(); } 