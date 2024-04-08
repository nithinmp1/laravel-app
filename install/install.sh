#!/bin/bash

# Function for error handling
handle_error() {
    echo "Error: $1" >&2
    exit 1
}

# Move contents of env.txt to .env
echo "Moving contents of env.txt to .env..."
cat env.txt > .env || handle_error "Failed to move contents of env.txt to .env"

composer update

php artisan key:generate

# Set up Docker containers
docker-compose up -d || handle_error "Failed to start Docker containers"

# Wait for containers to start
sleep_time=10
echo "Waiting for containers to start..."
sleep $sleep_time

# # Update Laravel dependencies
# echo "Updating Laravel dependencies..."
# docker-compose exec -T laravel_app composer update || handle_error "Composer update failed"

# Run database migrations
echo "Running database migrations..."
docker-compose exec -T laravel_app php artisan migrate || handle_error "Database migration failed"

echo "Database migration completed successfully."
