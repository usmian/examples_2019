#!/usr/bin/env bash

# проверяем, что запущено не под sudo
if [ $SUDO_UID ]
then
echo 'Не запускайте установку под sudo'
exit 1
fi

# первым делом цепляем shh-agent
eval $(ssh-agent)
ssh-add

# создаем общую сеть, если нужно
NETWORK=home-network
if [ $(sudo docker network ls | grep $NETWORK | wc -l) -lt 1 ]
then
    sudo docker network create --driver=bridge $NETWORK
else
    echo "общая docker сеть уже была создана"
fi

# устанавливаем переменные окружения и поднимаем контейнеры (делается по sudo, чтобы работало с любым докером, даже если не настроены нормально права на docker.sock)
sudo SSH_AUTH_SOCK=$SSH_AUTH_SOCK docker-compose -p laravel-service up -d
# копируем файл окружения из дефолтного, если файла .env ещё не существует
if [ ! -e .env ] # если файла .env не существует
then
    cp docker/.env.docker .env && sudo chmod 0777 .env && sudo chown $(id -u):$(id -g) .env
fi

# создаем папку с кешем композера если надо
test -e storage || mkdir storage
# Ставим полный доступ на папку с кешами
sudo chmod -R 777 storage

# создаем папку с кешем композера если надо
test -e ~/.composer || mkdir ~/.composer


# запускаем композер
if [ ! -e vendor ] # если vendor не существует
then
    sudo docker exec -u $(id -u):$(id -g) laravel-php composer install --optimize-autoloader --no-interaction
else
    sudo docker exec -u $(id -u):$(id -g) laravel-php composer update  --optimize-autoloader --no-interaction
fi
# генерим ключ
sudo docker exec -u $(id -u):$(id -g) laravel-php php artisan key:generate
# ждем, чтоб успела заработать сеть
sleep 15
# накатываем миграции
sudo docker exec -u $(id -u):$(id -g) laravel-php php artisan migrate

#в конце убираем за собой только что запущенный ssh-agent
ssh-agent -k
