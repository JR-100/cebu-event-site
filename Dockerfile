FROM php:8.2-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite mbstring exif pcntl bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chmod -R 775 storage bootstrap/cache

# Copy environment file
RUN cp .env.example .env || true

# Create SQLite database
RUN mkdir -p database && touch database/database.sqlite

# Generate app key, run migrations, and create storage link
RUN php artisan key:generate --force \
    && php artisan migrate --force \
    && php artisan storage:link

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
