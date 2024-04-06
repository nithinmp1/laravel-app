#!/bin/bash

# Set up Docker containers
docker-compose up -d

# Wait for containers to start
sleep 10

# Run database migrations
docker-compose exec -T laravel_app_1 php artisan migrate