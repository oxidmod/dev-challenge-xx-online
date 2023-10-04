#!/usr/bin/env bash

docker-compose up -d \
  && docker-compose exec app php composer.phar install \
  && docker-compose exec app php composer.phar migrate \
  && docker-compose restart app

echo 'Server is running on http://localhost:8080/api/v1'
