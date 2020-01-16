#!/bin/bash
nohup php /app/src/processor.php &
touch /app/src/run.log
tail -f /app/src/run.log