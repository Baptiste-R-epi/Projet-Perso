<?php
stream_set_blocking(STDIN, 0);
sleep(1);
echo fgets(STDIN);
echo "\n";