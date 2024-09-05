#!/bin/bash

docker run \
    --platform linux/amd64 \
    --name php_fun \
    -i \
    -t \
    -p "80:80" \
    -v ${PWD}/app:/app \
    -v ${PWD}/mysql:/var/lib/mysql mattrayner/lamp:latest-1804
