#!/usr/bin/env bash

docker exec opencaching-webserver ./psh.phar travis-ci:code-style

docker exec opencaching-webserver ./psh.phar travis-ci:unit
