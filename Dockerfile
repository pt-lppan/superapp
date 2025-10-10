# Gunakan base image resmi PHP 7.4-FPM
FROM php:7.4-fpm

# Install dependensi sistem dan ekstensi PHP
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql mysqli zip

RUN docker-php-ext-install calendar
# Aktifkan OPcache dengan menyalin file konfigurasi kustom
COPY php.ini /usr/local/etc/php/php.ini

# Set working directory
WORKDIR /var/www/html

# Salin semua file proyek ke working directory
COPY . .