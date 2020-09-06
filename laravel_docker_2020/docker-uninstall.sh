#!/usr/bin/env bash
sudo docker-compose -p billing down
sudo rm -R docker/mysql_data
sudo rm -R docker/mysql_log
sudo rm -R docker/nginx_log
