FROM php:7.4-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libicu-dev \
    libc-client-dev \
    libkrb5-dev \
    zip \
    unzip \
    libssl-dev \
    libmagickwand-dev \
    imagemagick \
    && rm -r /var/lib/apt/lists/*

# Configurar e instalar extensiones PHP
# mbstring, soap, zip, mysql, curl, gd, xml, intl, imap, imagick
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mysqli \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        soap \
        xml \
        intl \
        imap \
        opcache

# Instalar imagick via PECL (versión compatible con PHP 7.4)
RUN pecl install imagick-3.6.0 && docker-php-ext-enable imagick

# Instalar Composer 2
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurar PHP para rendimiento
RUN echo "upload_max_filesize=100M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "memory_limit=512M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/custom.ini

# Configurar OPcache para mejor rendimiento
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=10000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Configurar realpath cache (CRÍTICO para rendimiento en Docker)
RUN echo "realpath_cache_size=4096K" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "realpath_cache_ttl=600" >> /usr/local/etc/php/conf.d/custom.ini

# Optimizaciones adicionales de PHP-FPM
RUN echo "[www]" > /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "pm = dynamic" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "pm.max_children = 20" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "pm.start_servers = 4" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "pm.min_spare_servers = 2" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "pm.max_spare_servers = 6" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "pm.max_requests = 500" >> /usr/local/etc/php-fpm.d/zz-performance.conf

WORKDIR /var/www/html
