#!/bin/bash
set -e

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

# Create SQLite database file if it doesn't exist
if [ ! -f database/database.sqlite ]; then
    echo "Creating SQLite database..."
    touch database/database.sqlite
fi

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Clear cache and configs
echo "Clearing cache..."
php artisan config:clear
php artisan cache:clear

# Execute the command passed to the container
exec "$@"
