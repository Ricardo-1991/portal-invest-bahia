#!/bin/sh
set -e

# Garante a estrutura de storage (o volume compartilhado pode iniciar vazio).
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views \
         storage/logs storage/app/public
chown -R www-data:www-data storage bootstrap/cache

# Executa migrations e otimizações apenas no container da aplicação (não em workers).
if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
fi

php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
