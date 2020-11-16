Build project:
docker-compose up -d

After build go into container:
docker exec -it laravel-star-wars-api_app_1 bash

Build dependencies:
composer install
php artisan migrate

Full API documentation is available on page /api/documentation

Run PHP CS FIXER:
vendor/bin/php-cs-fixer fix






