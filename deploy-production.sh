#!/bin/bash

# SACO System Production Deployment Script
# For DigitalOcean and other cloud platforms

set -e

echo "🚀 Starting SACO System Production Deployment..."

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Check if .env file exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file from template..."
    cp .env.production .env
    echo "⚠️  Please edit .env file with your production values before continuing!"
    echo "   Required: DB_PASSWORD, APP_KEY, APP_URL, REDIS_PASSWORD"
    read -p "Press Enter after configuring .env file..."
fi

# Create necessary directories
echo "📁 Creating required directories..."
mkdir -p mysql
mkdir -p php

# Create MySQL configuration
if [ ! -f mysql/my.cnf ]; then
    echo "⚙️  Creating MySQL configuration..."
    cat > mysql/my.cnf << EOF
[mysqld]
# General Settings
default-storage-engine=InnoDB
innodb_buffer_pool_size=256M
innodb_log_file_size=64M
innodb_flush_log_at_trx_commit=2
innodb_flush_method=O_DIRECT

# Performance Settings
max_connections=100
query_cache_size=32M
query_cache_type=1
tmp_table_size=64M
max_heap_table_size=64M

# Security
local-infile=0

# Logging
log_error=/var/log/mysql/error.log
slow_query_log=1
slow_query_log_file=/var/log/mysql/slow.log
long_query_time=2

# Character Set
character-set-server=utf8mb4
collation-server=utf8mb4_unicode_ci
EOF
fi

# Create PHP configuration
if [ ! -f php/php.ini ]; then
    echo "⚙️  Creating PHP configuration..."
    cat > php/php.ini << EOF
; Production PHP Configuration
memory_limit=256M
max_execution_time=60
max_input_vars=3000
upload_max_filesize=20M
post_max_size=25M

; Error Reporting
display_errors=Off
log_errors=On
error_log=/var/log/php_errors.log

; Performance
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1

; Security
expose_php=Off
allow_url_fopen=Off
EOF
fi

# Generate application key if not set
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Optimize application
echo "⚡ Optimizing application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Clear and cache
php artisan cache:clear
php artisan config:clear

# Stop existing containers
echo "🛑 Stopping existing containers..."
docker-compose -f docker-compose.prod.yml down

# Build and start containers
echo "🐳 Starting production containers..."
docker-compose -f docker-compose.prod.yml up -d --build

# Wait for containers to be healthy
echo "⏳ Waiting for containers to be ready..."
sleep 30

# Check container health
echo "🔍 Checking container health..."
if docker-compose -f docker-compose.prod.yml ps | grep -q "Up (healthy)"; then
    echo "✅ All containers are healthy!"
else
    echo "❌ Some containers may not be healthy. Check logs:"
    docker-compose -f docker-compose.prod.yml logs
    exit 1
fi

# Setup SSL (optional)
read -p "Do you want to setup SSL with Let's Encrypt? (y/n): " setup_ssl
if [ "$setup_ssl" = "y" ]; then
    echo "🔒 Setting up SSL certificate..."
    # Add SSL setup commands here
    echo "SSL setup would be implemented here"
fi

echo "🎉 SACO System deployment completed successfully!"
echo "🌐 Your application should be available at: $(grep APP_URL .env | cut -d'=' -f2)"
echo "📊 Monitor with: docker-compose -f docker-compose.prod.yml logs -f"
echo "🔄 Restart with: docker-compose -f docker-compose.prod.yml restart"
