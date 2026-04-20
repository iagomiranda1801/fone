#!/bin/bash
set -e

cd /var/www/html

echo "Installing dependencies..."
composer update --no-interaction --optimize-autoloader

if [ ! -f .env ]; then
    cp .env.example .env
fi

php artisan key:generate --force --quiet 2>/dev/null || true

echo "Running migrations..."
php artisan migrate --force

echo "Running seeders..."
php artisan db:seed --force

exec php artisan serve --host=0.0.0.0 --port=8000
