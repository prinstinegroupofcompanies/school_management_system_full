FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    git curl unzip libpng-dev libjpeg-dev libfreetype6-dev libwebp-dev libzip-dev libpq-dev libonig-dev libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo pdo_pgsql pdo_sqlite mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html
COPY . /var/www/html

RUN chmod -R 775 storage bootstrap/cache || true

ENV PORT=8080
EXPOSE 8080

CMD sh -lc 'php artisan serve --host=0.0.0.0 --port=${PORT}'
