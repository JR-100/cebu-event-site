#!/bin/bash
set -e

echo "==> Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Seeding database (if empty)..."
php artisan db:seed --force 2>/dev/null || echo "Seeding skipped (already seeded or error)"

echo "==> Creating storage link..."
php artisan storage:link 2>/dev/null || true

echo "==> Starting server on port 10000..."
php artisan serve --host=0.0.0.0 --port=10000
