# ---------------------------------------------------------
# projects/app-lain/docker/Dockerfile
# PHP-FPM 8.2 untuk App Lain dengan Git, Composer, .htaccess
# ---------------------------------------------------------
FROM php:8.2-fpm

LABEL maintainer="app-lain" \
      org.opencontainers.image.title="app-lain:php-8.2" \
      org.opencontainers.image.description="PHP-FPM 8.2 for App Lain with Git, Composer, .htaccess support"

# Set timezone & install dependencies
ENV TZ=Asia/Jakarta
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip libzip-dev libpng-dev libjpeg62-turbo-dev libfreetype6-dev \
    libonig-dev libxml2-dev libicu-dev libxslt1.1 libxslt1-dev \
    libssl-dev pkg-config libmagickwand-dev apache2-utils \
    && rm -rf /var/lib/apt/lists/*

# Configure GD with JPEG and FreeType support
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# Install PHP extensions
RUN docker-php-ext-install -j$(nproc) \
    gd \
    intl \
    mbstring \
    xml \
    xsl \
    zip \
    pdo \
    pdo_mysql \
    bcmath \
    opcache

# Install Redis extension via PECL
RUN pecl install redis \
    && docker-php-ext-enable redis

# Configure OpCache for better performance
RUN { \
    echo "opcache.enable=1"; \
    echo "opcache.enable_cli=1"; \
    echo "opcache.memory_consumption=256"; \
    echo "opcache.interned_strings_buffer=16"; \
    echo "opcache.max_accelerated_files=20000"; \
    echo "opcache.validate_timestamps=1"; \
    echo "opcache.revalidate_freq=2"; \
} > /usr/local/etc/php/conf.d/opcache.ini

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Create non-root user for security
RUN useradd -ms /bin/bash www && chown -R www:www /var/www/html
USER www
