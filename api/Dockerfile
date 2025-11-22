# Laravel API Dockerfile for Development

FROM php:8.2-cli

# Set working directory
WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    sqlite3 \
    libsqlite3-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-jpeg --with-freetype
RUN docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_sqlite \
    gd \
    opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set PHP configuration for development
RUN echo "memory_limit = 512M" > /usr/local/etc/php/conf.d/laravel.ini
RUN echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/laravel.ini
RUN echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/laravel.ini
RUN echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/laravel.ini

# Copy composer files
COPY composer.json composer.lock ./

# Create necessary directories
RUN mkdir -p bootstrap/cache storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs

# Install Composer dependencies
RUN composer install --no-scripts --no-autoloader

# Copy application files
COPY . .

# Run composer autoload
RUN composer dump-autoload --optimize

# Expose port for Laravel development server
EXPOSE 8000

# Set up entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
