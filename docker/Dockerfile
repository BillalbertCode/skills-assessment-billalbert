# Use PHP 8.2 FPM as base image
FROM php:8.2-fpm

# Install essential system packages
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Copy PHP extension installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install required PHP extensions
RUN install-php-extensions \
    bcmath \
    gd \
    zip \
    intl \
    pdo_mysql \
    opcache \
    pcntl

# Copy Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Node.js and npm
COPY --from=node:20 /usr/local/bin/node /usr/local/bin/node
COPY --from=node:20 /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm

# Set working directory
WORKDIR /var/www

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 8080
EXPOSE 8080

# Set entrypoint
ENTRYPOINT ["entrypoint.sh"]
