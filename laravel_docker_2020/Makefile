# остановить контейнеры, удалить их. Удалить собранные образы docker и все собрать с нуля
rebuild: remove  compose

remove:
	docker stop laravel-php laravel-nginx laravel-mysql && \
	docker rm laravel-php laravel-nginx laravel-mysql

compose:
	./docker-install.sh

migration-refresh:
	docker exec -it laravel-php php artisan migrate:refresh

start:
	docker start laravel-php laravel-nginx laravel-mysql

uninstall:
	./docker-uninstall.sh

ps:
	sudo docker ps --format='table{{.ID}}\t{{.Names}}\t{{.Status}}\t{{.Ports}}'

php:
	sudo docker exec -it laravel-php bash

permissions:
	sudo chown ${USER}:${USER} docker -R

make-auth:
	sudo docker exec -it laravel-php php artisan make:auth