FROM dunglas/frankenphp:php8.2-bookworm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    gd \
    zip \
    pdo \
    pdo_mysql

# Set working directory
WORKDIR /app

# Copy application files
COPY . .

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Allow composer to run as root (required in Docker)
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Expose port (Railway uses 8080 internally)
EXPOSE 8080

# Start FrankenPHP (built-in Caddy server)
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]