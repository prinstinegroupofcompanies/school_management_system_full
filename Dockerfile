FROM php:8.3-cli

# Install system dependencies and PHP extensions
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       git \
       unzip \
       pkg-config \
       libpng-dev \
       libjpeg62-turbo-dev \
       libfreetype6-dev \
       libzip-dev \
       libpq-dev \
       libonig-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
       pdo \
       pdo_mysql \
       pdo_pgsql \
       mbstring \
       gd \
       zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_NO_INTERACTION=1

WORKDIR /var/www/html

# Leverage Docker layer caching for Composer deps
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-scripts --optimize-autoloader

# Copy application code (now artisan exists)
COPY . .
# Finish Composer scripts after full code is present
RUN composer dump-autoload -o && composer install --no-dev --prefer-dist --optimize-autoloader || true

# Expose the port Render will map
ENV PORT=8080
EXPOSE 8080

# Entrypoint: prepare app then run server
# - storage:link may fail if already linked, so ignore error
# - migrate with --force; ignore errors if DB not reachable to avoid boot failure
CMD sh -lc 'php artisan storage:link >/dev/null 2>&1 || true; \
            php artisan config:cache >/dev/null 2>&1 || true; \
            php artisan route:cache >/dev/null 2>&1 || true; \
            php artisan view:cache >/dev/null 2>&1 || true; \
            php artisan migrate --force >/dev/null 2>&1 || true; \
            php -S 0.0.0.0:${PORT} public/index.php'


