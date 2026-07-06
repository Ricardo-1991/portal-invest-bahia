# syntax=docker/dockerfile:1

# ---------- Estágio 1: dependências PHP (sem dev) ----------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# As extensões de plataforma (intl, exif, gd...) existem no runtime, não nesta imagem.
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts --ignore-platform-reqs

# ---------- Estágio 2: build dos assets front-end ----------
FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
RUN npm ci
COPY resources ./resources
# O app.css referencia (via @source) as views de paginação do framework; precisam
# existir para o Tailwind gerar as classes usadas na paginação.
COPY --from=vendor /app/vendor/laravel/framework/src/Illuminate/Pagination/resources/views \
     ./vendor/laravel/framework/src/Illuminate/Pagination/resources/views
RUN npm run build

# ---------- Estágio 3: runtime PHP-FPM ----------
FROM php:8.5-fpm AS runtime

# Extensões necessárias (PostgreSQL, imagens, i18n, zip) via instalador da
# comunidade, que resolve libs e particularidades de versão do PHP.
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_pgsql pgsql gd zip intl bcmath exif opcache

# Configurações de produção do PHP (opcache).
COPY docker/production/php.ini /usr/local/etc/php/conf.d/99-pib.ini

WORKDIR /var/www/html

# Código + dependências + assets compilados.
COPY --from=vendor /app /var/www/html
COPY --from=assets /app/public/build /var/www/html/public/build

# Permissões nas pastas graváveis.
RUN chown -R www-data:www-data storage bootstrap/cache

COPY docker/production/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

EXPOSE 9000
ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]

# ---------- Estágio 4: servidor web (Nginx) ----------
FROM nginx:alpine AS web
COPY docker/production/nginx.conf /etc/nginx/conf.d/default.conf
# Estáticos do app + assets compilados; symlink para uploads no volume compartilhado.
COPY --from=vendor /app/public /var/www/html/public
COPY --from=assets /app/public/build /var/www/html/public/build
RUN ln -sf /var/www/html/storage/app/public /var/www/html/public/storage
