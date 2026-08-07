FROM php:8.3-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libwebp-dev \
    libzip-dev \
    libpq-dev \
    libonig-dev \
    libsqlite3-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    pdo_sqlite \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1

WORKDIR /var/www/html
COPY composer.json composer.lock* ./
RUN composer install --no-dev --prefer-dist --optimize-autoloader --no-scripts || composer update --no-dev --prefer-dist --optimize-autoloader --no-scripts

COPY . .

RUN php artisan key:generate --force || true \
    && php artisan config:cache || true \
    && php artisan route:cache || true \
    && php artisan view:cache || true \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache storage/app/public \
    && chmod -R 775 storage bootstrap/cache

ENV PORT=8080
EXPOSE 8080

CMD sh -lc 'php artisan serve --host=0.0.0.0 --port=${PORT}'
