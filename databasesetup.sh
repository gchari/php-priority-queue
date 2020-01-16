#!/bin/bash
php artisan migrate
ret=$?
while [ $ret -ne 0 ] ; do
    echo "Waiting for mysql Server"
    sleep 2
    php artisan migrate
	ret=$?
done
