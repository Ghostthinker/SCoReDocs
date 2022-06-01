#!/bin/bash
# updates the connection config of the xdebug server in the container with the current ip of gitpod.
# It can be used when the debugger doesn't connect because gitpods ip has dynamically changed.
docker exec -it $(docker ps | grep "webserver" | awk '{ print $1 }') bash -c "cd /usr/local/etc/php/conf.d/ && sed -i '/xdebug.remote_host/d' ./docker-php-ext-xdebug.ini && echo $'xdebug.remote_host=$HOST_IP' >> ./docker-php-ext-xdebug.ini"
