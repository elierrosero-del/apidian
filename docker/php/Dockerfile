# ============================================
# DOCKERFILE PHP 7.3 - OPTIMIZADO PARA MÁXIMO RENDIMIENTO
# ============================================
FROM php:7.3-fpm-alpine AS base

# Variables de optimización
ENV PHP_OPCACHE_ENABLE=1 \
    PHP_OPCACHE_REVALIDATE_FREQ=0 \
    PHP_OPCACHE_VALIDATE_TIMESTAMPS=0 \
    PHP_OPCACHE_MAX_ACCELERATED_FILES=20000 \
    PHP_OPCACHE_MEMORY_CONSUMPTION=512 \
    PHP_OPCACHE_MAX_WASTED_PERCENTAGE=10 \
    PHP_OPCACHE_INTERNED_STRINGS_BUFFER=64 \
    PHP_OPCACHE_FAST_SHUTDOWN=1 \
    PHP_REALPATH_CACHE_SIZE=4096K \
    PHP_REALPATH_CACHE_TTL=600

# Instalar dependencias del sistema (Alpine para menor tamaño)
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        curl-dev \
        imagemagick-dev \
        libtool \
        libxml2-dev \
        postgresql-dev \
        sqlite-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libzip-dev \
        icu-dev \
        imap-dev \
        krb5-dev \
        openssl-dev \
        c-client \
    && apk add --no-cache \
        curl \
        git \
        imagemagick \
        freetype \
        libjpeg-turbo \
        libpng \
        libzip \
        icu \
        imap \
        krb5 \
        openssl \
        zip \
        unzip

# Configurar e instalar extensiones PHP (optimizado para velocidad)
RUN docker-php-ext-configure gd \
        --with-freetype-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-configure imap \
        --with-kerberos \
        --with-imap-ssl \
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

# Instalar imagick (versión optimizada)
RUN pecl install imagick-3.5.1 \
    && docker-php-ext-enable imagick

# Limpiar dependencias de build
RUN apk del .build-deps

# Instalar Composer (versión específica para mejor cache)
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# ============================================
# CONFIGURACIÓN PHP OPTIMIZADA PARA MÁXIMO RENDIMIENTO
# ============================================

# PHP.ini optimizado para producción
RUN echo "# CONFIGURACIÓN OPTIMIZADA PARA MÁXIMO RENDIMIENTO" > /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "# Memory y Execution" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "memory_limit=1024M" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "max_execution_time=300" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "max_input_time=300" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "default_socket_timeout=300" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "# Upload optimizado" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "upload_max_filesize=100M" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "max_file_uploads=50" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "# Realpath Cache (CRÍTICO para Docker)" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "realpath_cache_size=${PHP_REALPATH_CACHE_SIZE}" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "realpath_cache_ttl=${PHP_REALPATH_CACHE_TTL}" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "# Optimizaciones adicionales" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "expose_php=Off" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "log_errors=On" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "log_errors_max_len=0" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "ignore_repeated_errors=On" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "ignore_repeated_source=On" >> /usr/local/etc/php/conf.d/99-performance.ini \
    && echo "html_errors=Off" >> /usr/local/etc/php/conf.d/99-performance.ini

# OPcache optimizado para máximo rendimiento
RUN echo "# OPCACHE - CONFIGURACIÓN EXTREMA PARA RENDIMIENTO" > /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.enable=${PHP_OPCACHE_ENABLE}" >> /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.memory_consumption=${PHP_OPCACHE_MEMORY_CONSUMPTION}" >> /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.interned_strings_buffer=${PHP_OPCACHE_INTERNED_STRINGS_BUFFER}" >> /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.max_accelerated_files=${PHP_OPCACHE_MAX_ACCELERATED_FILES}" >> /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.max_wasted_percentage=${PHP_OPCACHE_MAX_WASTED_PERCENTAGE}" >> /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.validate_timestamps=${PHP_OPCACHE_VALIDATE_TIMESTAMPS}" >> /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.revalidate_freq=${PHP_OPCACHE_REVALIDATE_FREQ}" >> /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.fast_shutdown=${PHP_OPCACHE_FAST_SHUTDOWN}" >> /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.save_comments=0" >> /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.load_comments=0" >> /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.enable_file_override=1" >> /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.optimization_level=0x7FFEBFFF" >> /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.inherited_hack=1" >> /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.dups_fix=1" >> /usr/local/etc/php/conf.d/10-opcache.ini \
    && echo "opcache.blacklist_filename=" >> /usr/local/etc/php/conf.d/10-opcache.ini

# ============================================
# PHP-FPM OPTIMIZADO PARA MÁXIMO RENDIMIENTO
# ============================================
RUN echo "# PHP-FPM OPTIMIZADO PARA MÁXIMO RENDIMIENTO" > /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "[www]" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "# Process Manager optimizado" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "pm = dynamic" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "pm.max_children = 50" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "pm.start_servers = 10" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "pm.min_spare_servers = 5" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "pm.max_spare_servers = 15" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "pm.max_requests = 1000" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "# Timeouts optimizados" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "request_terminate_timeout = 300s" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "request_slowlog_timeout = 10s" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "slowlog = /proc/self/fd/2" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "# Optimizaciones de red" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "listen.backlog = 1024" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "# Logging optimizado" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "catch_workers_output = yes" >> /usr/local/etc/php-fpm.d/zz-performance.conf \
    && echo "decorate_workers_output = no" >> /usr/local/etc/php-fpm.d/zz-performance.conf

# Crear usuario no-root para seguridad
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -D -S -G www www

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Cambiar ownership
RUN chown -R www:www /var/www/html

# Usar usuario no-root
USER www

# Healthcheck optimizado
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD php-fpm -t || exit 1

# Exponer puerto
EXPOSE 9000

# Comando por defecto
CMD ["php-fpm"]
