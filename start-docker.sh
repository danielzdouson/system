#!/bin/bash

# Docker Auto-Start Script for SACO System
# This script ensures Docker containers start automatically

echo "Starting SACO System containers..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "Docker is not running. Starting Docker..."
    sudo systemctl start docker
    sleep 5
fi

# Navigate to project directory
cd /home/danielz/projects/system

# Stop any existing containers (clean start)
echo "Stopping existing containers..."
docker-compose down

# Start containers
echo "Starting containers..."
docker-compose up -d --force-recreate

# Wait for containers to be ready
echo "Waiting for containers to initialize..."
sleep 30

# Fix permissions (this is the key fix for the 500 error)
echo "Fixing file permissions..."
docker exec saco_system_php chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker exec saco_system_php chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Check container status
echo "Checking container status..."
docker-compose ps

# Test if the application is responding
echo "Testing application response..."
if curl -f http://localhost:8080 > /dev/null 2>&1; then
    echo "✅ SUCCESS: Application is running correctly!"
else
    echo "❌ WARNING: Application may not be responding properly"
    echo "Check logs with: docker logs saco_system_php"
fi

echo "SACO System startup complete!"
echo "Application should be available at: http://localhost:8080"
echo "Database available at: localhost:3306"
