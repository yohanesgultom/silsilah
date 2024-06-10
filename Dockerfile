FROM node:16-buster AS node
FROM php:7.4-fpm

# nodejs
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
COPY --from=node /usr/local/bin/node /usr/local/bin/node
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm

# essentials
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    locales \
    jpegoptim optipng pngquant gifsicle \
    zip \
    unzip \
    git \
    curl

# Laravel
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN apt-get clean && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

EXPOSE 9000