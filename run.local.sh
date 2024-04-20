cp .env.local .env

docker compose exec ioz_php composer install

docker compose exec ioz_php php artisan key:generate

docker compose exec ioz_php php artisan storage:link

docker compose exec ioz_php php artisan migrate

docker compose exec ioz_php php artisan db:seed
