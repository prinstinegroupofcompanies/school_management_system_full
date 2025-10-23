# Use PHP 8.3 with Alpine for Fly.io deployment
FROM php:8.3-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    postgresql-dev \
    sqlite \
    nodejs \
    npm \
    freetype-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
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
        zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Copy package.json and install Node dependencies
COPY package.json package-lock.json ./
RUN npm ci --only=production

# Copy application code
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www

# Install remaining dependencies and run post-install scripts
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Build frontend assets
RUN npm run build || echo "No build script found, skipping..."

# Create necessary directories
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache storage/app/public
RUN chmod -R 775 storage bootstrap/cache

# Create SQLite database directory
RUN mkdir -p /database
RUN chmod 755 /database

# Copy environment file
COPY .env.fly .env

# Generate application key
RUN php artisan key:generate --force

# Cache configuration for production
RUN php artisan config:cache
RUN php artisan route:cache
RUN php artisan view:cache

# Create symbolic link for storage
RUN php artisan storage:link

# Expose port
EXPOSE 8080

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8080/health || exit 1

# Start the application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
