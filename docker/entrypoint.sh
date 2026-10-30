#!/bin/sh
set -eu

if [ -z "${APP_KEY:-}" ]; then
    echo 'APP_KEY is required. Generate one with php artisan key:generate --show and set it in Coolify.' >&2
    exit 1
fi

cd /var/www/html
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

# Cache runtime environment values only after Coolify injects them.
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache

exec docker-php-entrypoint "$@"
