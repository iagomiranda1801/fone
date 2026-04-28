#!/bin/bash
set -e

cd /var/www/html

export COMPOSER_PROCESS_TIMEOUT=0

echo "Installing dependencies..."
if [ ! -d vendor ] || [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --optimize-autoloader --no-progress
else
    echo "Vendor already present, skipping composer install."
fi

if [ ! -f .env ]; then
    cp .env.example .env
fi

php artisan key:generate --force --quiet 2>/dev/null || true

echo "Running migrations..."
php artisan migrate --force

echo "Running seeders..."
php artisan db:seed --force

exec php artisan serve --host=0.0.0.0 --port=8000
