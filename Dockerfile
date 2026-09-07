FROM php:8.3-cli-alpine

# Install system dependencies and PHP extensions for Laravel & MySQL
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip \
    linux-headers \
    $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_mysql bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 8005

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8005"]
