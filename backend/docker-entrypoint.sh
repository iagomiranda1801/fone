#!/bin/bash
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
    echo "Installing dependencies..."
    composer install --no-interaction --optimize-autoloader
fi

if [ ! -f .env ]; then
    cp .env.example .env
fi

php artisan key:generate --force --quiet 2>/dev/null || true

exec php artisan serve --host=0.0.0.0 --port=8000
