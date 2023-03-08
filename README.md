# Установка
`git clone git@github.com:biohazardhome/laravel-amocrm-api.git`

В каталоге с проектом запустить `cd laravel-amocrm-api`
```
docker run --rm \
    --pull=always \
    -v "$(pwd)":/opt \
    -w /opt \
    laravelsail/php82-composer:latest \
    bash -c "composer install && php ./artisan sail:install --with=pgsql,redis,meilisearch,mailpit,selenium"
```
```
./vendor/bin/sail up -d 
./vendor/bin/sail artisan sail:install --with=pgsql,redis,meilisearch,mailpit,selenium
./vendor/bin/sail pull pgsql redis meilisearch mailpit selenium
./vendor/bin/sail build
./vendor/bin/sail artisan migrate
```
Routs
```
http://localhost/lead/fetch/ Выгружается сделки в базу данных
http://localhost/lead/ Отображаются загруженные в базу данных сделки 
http://localhost/amocrm-api/get-token2/{code} - Обновление файла токена

```
