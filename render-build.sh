#!/usr/bin/env bash
set -e

echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader

echo "Creating SQLite database..."
mkdir -p database
touch database/database.sqlite

echo "Generating app key..."
php artisan key:generate --force

echo "Running migrations..."
php artisan migrate --force

echo "Creating storage link..."
php artisan storage:link || true

echo "Caching config..."
php artisan config:cache
php artisan route:cache

echo "Build complete!"
