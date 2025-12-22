#!/bin/bash

# ============================================
# APIDIAN - Script de Instalación Docker
# Facturación Electrónica DIAN Colombia
# Compatible con Laravel 5.8 / PHP 7.4
# ============================================
# Uso: chmod +x install.sh && sudo ./install.sh
# ============================================

set -e

# Colores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}"
echo "============================================"
echo "   APIDIAN - Instalación Docker"
echo "   Facturación Electrónica DIAN Colombia"
echo "============================================"
echo -e "${NC}"

# Verificar root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Error: Ejecutar como root (sudo ./install.sh)${NC}"
    exit 1
fi

# Directorio actual
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# Generar contraseñas seguras
DB_PASSWORD=$(openssl rand -base64 12 | tr -dc 'a-zA-Z0-9' | head -c 16)
DB_ROOT_PASSWORD=$(openssl rand -base64 12 | tr -dc 'a-zA-Z0-9' | head -c 16)

# Configuración
echo -e "${YELLOW}Configuración:${NC}"
read -p "Puerto HTTP [80]: " HTTP_PORT
HTTP_PORT=${HTTP_PORT:-80}

read -p "Puerto MySQL externo [3306]: " MYSQL_PORT
MYSQL_PORT=${MYSQL_PORT:-3306}

echo ""
echo -e "${GREEN}Configuración seleccionada:${NC}"
echo "  Puerto HTTP: $HTTP_PORT"
echo "  Puerto MySQL: $MYSQL_PORT"
echo ""

read -p "¿Continuar? (s/n): " confirm
[[ "$confirm" != "s" && "$confirm" != "S" ]] && exit 0

# ============================================
# 1. INSTALAR DOCKER
# ============================================
echo -e "${YELLOW}[1/8] Verificando Docker...${NC}"

if ! command -v docker &> /dev/null; then
    echo "Instalando Docker..."
    apt-get update
    apt-get install -y apt-transport-https ca-certificates curl gnupg lsb-release
    
    curl -fsSL https://download.docker.com/linux/ubuntu/gpg | gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg
    
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null
    
    apt-get update
    apt-get install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
    
    systemctl enable docker
    systemctl start docker
fi
echo -e "${GREEN}✓ Docker instalado${NC}"

# ============================================
# 2. CREAR DOCKERFILE PHP 7.4 (IDÉNTICO AL LOCAL)
# ============================================
echo -e "${YELLOW}[2/8] Creando Dockerfile PHP 7.4...${NC}"

mkdir -p docker/php docker/nginx

cat > docker/php/Dockerfile << 'DOCKERFILE'
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
DOCKERFILE

echo -e "${GREEN}✓ Dockerfile creado${NC}"

# ============================================
# 3. CREAR CONFIGURACIÓN NGINX
# ============================================
echo -e "${YELLOW}[3/8] Creando configuración Nginx...${NC}"

cat > docker/nginx/default.conf << 'NGINXCONF'
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;
    index index.php index.html;

    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.ht {
        deny all;
    }
}
NGINXCONF

echo -e "${GREEN}✓ Nginx configurado${NC}"

# ============================================
# 4. CREAR DOCKER-COMPOSE (IDÉNTICO AL LOCAL)
# ============================================
echo -e "${YELLOW}[4/8] Creando docker-compose.yml...${NC}"

cat > docker-compose.yml << DOCKERCOMPOSE
version: '3'

services:
    nginx:
        image: nginx:alpine
        container_name: apidian_nginx
        working_dir: /var/www/html
        ports:
            - "${HTTP_PORT}:80"
        volumes:
            - ./:/var/www/html:cached
            - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
        depends_on:
            - php
        networks:
            - api_dian
    php:
        build:
            context: ./docker/php
            dockerfile: Dockerfile
        container_name: apidian_php
        working_dir: /var/www/html
        volumes:
            - ./:/var/www/html:cached
        depends_on:
            - mariadb
        networks:
            - api_dian
        environment:
            - PHP_OPCACHE_ENABLE=1
            - PHP_OPCACHE_VALIDATE_TIMESTAMPS=0
    mariadb:
        image: mariadb:10.5
        container_name: apidian_mariadb
        environment:
            - MYSQL_USER=apidian
            - MYSQL_PASSWORD=${DB_PASSWORD}
            - MYSQL_DATABASE=apidian
            - MYSQL_ROOT_PASSWORD=${DB_ROOT_PASSWORD}
        ports:
            - "${MYSQL_PORT}:3306"
        volumes:
            - apidian_mysql_data:/var/lib/mysql
        networks:
            - api_dian
        command: --innodb-buffer-pool-size=256M --innodb-log-file-size=64M --innodb-flush-log-at-trx-commit=2

networks:
    api_dian:
        driver: "bridge"

volumes:
    apidian_mysql_data:
        driver: "local"
DOCKERCOMPOSE

echo -e "${GREEN}✓ docker-compose.yml creado${NC}"

# ============================================
# 5. CREAR ARCHIVO .ENV
# ============================================
echo -e "${YELLOW}[5/8] Creando archivo .env...${NC}"

APP_KEY="base64:$(openssl rand -base64 32)"
SERVER_IP=$(curl -s ifconfig.me 2>/dev/null || hostname -I | awk '{print $1}')

cat > .env << ENVFILE
APP_NAME="APIDIAN"
APP_VERSION=" v2.1"
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_PORT=${HTTP_PORT}
APP_URL=http://${SERVER_IP}
FORCE_HTTPS=false

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=apidian
DB_USERNAME=apidian
DB_PASSWORD=${DB_PASSWORD}

BROADCAST_DRIVER=log
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME=

ALLOW_PUBLIC_DOWNLOAD=true
APPLY_SEND_CUSTORMER_CREDENTIALS=true
GRAPHIC_REPRESENTATION_TEMPLATE=2
ALLOW_PUBLIC_REGISTER=true
VALIDATE_BEFORE_SENDING=true

# Docker Compose vars
MYSQL_USER=apidian
MYSQL_PASSWORD=${DB_PASSWORD}
MYSQL_DATABASE=apidian
MYSQL_ROOT_PASSWORD=${DB_ROOT_PASSWORD}
ENVFILE

echo -e "${GREEN}✓ .env creado${NC}"

# ============================================
# 6. CONSTRUIR E INICIAR CONTENEDORES
# ============================================
echo -e "${YELLOW}[6/8] Construyendo contenedores...${NC}"

docker compose build --no-cache
docker compose up -d

echo "Esperando a MariaDB (30s)..."
sleep 30

echo -e "${GREEN}✓ Contenedores iniciados${NC}"

# ============================================
# 7. INSTALAR DEPENDENCIAS Y CONFIGURAR
# ============================================
echo -e "${YELLOW}[7/8] Instalando dependencias...${NC}"

# Descomprimir storage.zip (según guía)
if [ -f "storage.zip" ]; then
    unzip -o storage.zip
fi

# Permisos (según guía: chmod -R 777 storage bootstrap/cache vendor/mpdf/mpdf)
chmod -R 777 storage bootstrap/cache

# Composer install
docker compose exec -T php composer install --no-dev --optimize-autoloader

# Ejecutar urn_on.sh (según guía - CRÍTICO para firma DIAN)
if [ -f "urn_on.sh" ]; then
    chmod 700 urn_on.sh
    ./urn_on.sh
fi

# Configuración Laravel
docker compose exec -T php php artisan key:generate --force
docker compose exec -T php php artisan config:cache
docker compose exec -T php php artisan cache:clear
docker compose exec -T php php artisan storage:link
docker compose exec -T php php artisan migrate --seed --force

# Permisos finales dentro del contenedor
docker compose exec -T php chmod -R 777 /var/www/html/storage
docker compose exec -T php chmod -R 777 /var/www/html/bootstrap/cache
docker compose exec -T php chmod -R 777 /var/www/html/vendor/mpdf/mpdf || true

# Limpiar caché final (según guía)
docker compose exec -T php php artisan config:cache
docker compose exec -T php php artisan config:clear
docker compose exec -T php php artisan cache:clear

echo -e "${GREEN}✓ Dependencias instaladas${NC}"

# ============================================
# 8. MOSTRAR INFORMACIÓN
# ============================================
echo -e "${YELLOW}[8/8] Finalizando...${NC}"

echo ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}   ¡INSTALACIÓN COMPLETADA!${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
echo -e "${BLUE}URL de la API:${NC}"
echo "  http://${SERVER_IP}:${HTTP_PORT}"
echo ""
echo -e "${BLUE}Base de datos:${NC}"
echo "  Host: localhost:${MYSQL_PORT}"
echo "  Database: apidian"
echo "  Usuario: apidian"
echo "  Password: ${DB_PASSWORD}"
echo "  Root Password: ${DB_ROOT_PASSWORD}"
echo ""
echo -e "${BLUE}Comandos útiles:${NC}"
echo "  Ver logs:     docker compose logs -f"
echo "  Reiniciar:    docker compose restart"
echo "  Detener:      docker compose down"
echo "  Estado:       docker compose ps"
echo ""

# Guardar credenciales
cat > CREDENCIALES.txt << CREDS
============================================
CREDENCIALES APIDIAN - $(date)
============================================

URL API: http://${SERVER_IP}:${HTTP_PORT}

Base de Datos:
  Host: localhost:${MYSQL_PORT} (externo) / mariadb:3306 (interno)
  Database: apidian
  Usuario: apidian
  Password: ${DB_PASSWORD}
  Root Password: ${DB_ROOT_PASSWORD}

============================================
CREDS
chmod 600 CREDENCIALES.txt

echo -e "${YELLOW}Credenciales guardadas en: CREDENCIALES.txt${NC}"
echo ""
echo -e "${GREEN}¡Listo! Accede a http://${SERVER_IP}:${HTTP_PORT}${NC}"
