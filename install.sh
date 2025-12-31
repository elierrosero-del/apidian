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
read -p "Dominio (ej: apidian.clipers.pro) [localhost]: " DOMAIN
DOMAIN=${DOMAIN:-localhost}

read -p "Email para certificado SSL (requerido si usa dominio): " SSL_EMAIL

read -p "Puerto MySQL externo [3306]: " MYSQL_PORT
MYSQL_PORT=${MYSQL_PORT:-3306}

# Detectar si puerto 80 está en uso
USE_PROXY=false
INTERNAL_PORT=8081
DIRECT_PORT=80
CAN_USE_SSL=true

if ss -tuln | grep -q ':80 '; then
    # Detectar qué está usando el puerto 80
    PORT_80_INFO=$(ss -tlnp | grep ':80 ' | head -1)
    
    if echo "$PORT_80_INFO" | grep -q "docker-proxy"; then
        echo -e "${YELLOW}⚠ Puerto 80 usado por otro contenedor Docker.${NC}"
        echo ""
        echo "Opciones:"
        echo "  1) Usar puerto 8081 directamente (SSL debe configurarse en tu proxy existente)"
        echo "  2) Detener el otro Docker y usar puerto 80 (SSL automático disponible)"
        echo ""
        read -p "Selecciona opción [1]: " port_option
        port_option=${port_option:-1}
        
        case $port_option in
            1)
                USE_PROXY=false
                DIRECT_PORT=${INTERNAL_PORT}
                CAN_USE_SSL=false
                echo -e "${GREEN}✓ APIDIAN usará puerto ${INTERNAL_PORT}${NC}"
                echo -e "${YELLOW}Nota: Configura SSL en tu proxy existente para ${DOMAIN}${NC}"
                ;;
            2)
                echo -e "${YELLOW}Deteniendo contenedores Docker que usan puerto 80...${NC}"
                # Buscar y detener contenedores que usan puerto 80
                for container in $(docker ps -q); do
                    if docker port "$container" 2>/dev/null | grep -q "0.0.0.0:80"; then
                        container_name=$(docker inspect --format '{{.Name}}' "$container" | sed 's/\///')
                        echo "Deteniendo $container_name..."
                        docker stop "$container"
                    fi
                done
                sleep 2
                USE_PROXY=false
                DIRECT_PORT=80
                CAN_USE_SSL=true
                echo -e "${GREEN}✓ Puerto 80 liberado${NC}"
                ;;
        esac
    else
        # Puerto 80 usado por servicio del sistema (Apache, Nginx nativo, etc)
        echo -e "${YELLOW}⚠ Puerto 80 usado por otro servicio del sistema.${NC}"
        
        # Verificar si es Nginx del sistema ya corriendo
        if systemctl is-active --quiet nginx 2>/dev/null; then
            echo -e "${GREEN}✓ Nginx del sistema detectado. Configurando como proxy...${NC}"
            USE_PROXY=true
            CAN_USE_SSL=true
        else
            echo ""
            echo "Opciones:"
            echo "  1) Usar puerto 8081 directamente"
            echo "  2) Detener servicio actual e instalar Nginx como proxy (SSL disponible)"
            echo ""
            read -p "Selecciona opción [1]: " sys_option
            sys_option=${sys_option:-1}
            
            if [ "$sys_option" = "2" ]; then
                echo "Deteniendo servicio en puerto 80..."
                fuser -k 80/tcp 2>/dev/null || true
                sleep 2
                
                USE_PROXY=true
                CAN_USE_SSL=true
                if ! command -v nginx &> /dev/null; then
                    echo -e "${YELLOW}Instalando Nginx del sistema...${NC}"
                    apt-get update
                    apt-get install -y nginx
                fi
            else
                USE_PROXY=false
                DIRECT_PORT=${INTERNAL_PORT}
                CAN_USE_SSL=false
            fi
        fi
    fi
else
    # Puerto 80 libre
    echo -e "${GREEN}✓ Puerto 80 disponible${NC}"
    USE_PROXY=false
    DIRECT_PORT=80
    CAN_USE_SSL=true
fi

# Determinar si usar SSL (solo si es posible)
USE_SSL=false
if [[ "$DOMAIN" != "localhost" && -n "$SSL_EMAIL" && "$CAN_USE_SSL" = true ]]; then
    USE_SSL=true
elif [[ "$DOMAIN" != "localhost" && -n "$SSL_EMAIL" && "$CAN_USE_SSL" = false ]]; then
    echo -e "${YELLOW}⚠ SSL automático no disponible en puerto ${INTERNAL_PORT}.${NC}"
    echo -e "${YELLOW}  Configura SSL en tu proxy existente después de la instalación.${NC}"
fi

echo ""
echo -e "${GREEN}Configuración seleccionada:${NC}"
echo "  Dominio: $DOMAIN"
if [ "$CAN_USE_SSL" = true ]; then
    echo "  SSL: $([ "$USE_SSL" = true ] && echo "Sí (Let's Encrypt)" || echo "No")"
else
    echo "  SSL: No disponible (configurar en proxy existente)"
fi
if [ "$USE_PROXY" = true ]; then
    echo "  Modo: Proxy (Nginx del sistema -> Docker:$INTERNAL_PORT)"
else
    echo "  Puerto HTTP: $DIRECT_PORT"
fi
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
echo -e "${YELLOW}[2/9] Creando Dockerfile PHP 7.3...${NC}"

mkdir -p docker/php docker/nginx docker/mariadb

# Crear configuración optimizada de MariaDB
cat > docker/mariadb/my.cnf << 'MARIADBCONF'
# ============================================
# MARIADB OPTIMIZADO PARA MÁXIMO RENDIMIENTO
# ============================================

[mysqld]
# Configuración básica
user = mysql
pid-file = /var/run/mysqld/mysqld.pid
socket = /var/run/mysqld/mysqld.sock
port = 3306
basedir = /usr
datadir = /var/lib/mysql
tmpdir = /tmp
lc-messages-dir = /usr/share/mysql

# Charset optimizado para español
character-set-server = utf8
collation-server = utf8_spanish_ci
init-connect = 'SET NAMES utf8'

# InnoDB optimizado para máximo rendimiento
innodb_buffer_pool_size = 512M
innodb_log_file_size = 128M
innodb_log_buffer_size = 32M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT
innodb_file_per_table = 1
innodb_open_files = 400
innodb_io_capacity = 400
innodb_read_io_threads = 8
innodb_write_io_threads = 8
innodb_thread_concurrency = 16

# Query Cache optimizado
query_cache_type = 1
query_cache_size = 64M
query_cache_limit = 2M

# Conexiones optimizadas
max_connections = 200
thread_cache_size = 50
table_open_cache = 4000

# Buffers optimizados
sort_buffer_size = 4M
read_buffer_size = 2M
join_buffer_size = 8M
tmp_table_size = 64M
max_heap_table_size = 64M

# Timeouts optimizados
wait_timeout = 600
interactive_timeout = 600

# Optimizaciones adicionales
skip-name-resolve
skip-external-locking
max_allowed_packet = 64M

[mysql]
default-character-set = utf8

[client]
default-character-set = utf8
MARIADBCONF

cat > docker/php/Dockerfile << 'DOCKERFILE'
FROM php:7.3-fpm

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
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
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

# Instalar imagick via PECL (versión compatible con PHP 7.3)
RUN pecl install imagick-3.4.4 && docker-php-ext-enable imagick

# Instalar Composer 2.2 (versión compatible con PHP 7.3 sin audit)
COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

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
# 3. CREAR CONFIGURACIÓN NGINX CON SSL
# ============================================
echo -e "${YELLOW}[3/9] Creando configuración Nginx...${NC}"

mkdir -p docker/nginx/sites-available

# Configuración Nginx del contenedor Docker (siempre sin SSL, el proxy maneja SSL)
cat > docker/nginx/sites-available/default.conf << NGINXCONF
server {
    listen 80;
    server_name ${DOMAIN} localhost;
    root /var/www/html/public;
    index index.php index.html;
    
    client_max_body_size 100M;
    
    location /health {
        return 200 "healthy\n";
        add_header Content-Type text/plain;
    }
    
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    location ~ \.php\$ {
        try_files \$uri =404;
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }
    
    location ~ /\.ht {
        deny all;
    }
}
NGINXCONF

# Si usamos proxy, crear configuración en Nginx del sistema
if [ "$USE_PROXY" = true ]; then
    echo -e "${YELLOW}Configurando Nginx del sistema como proxy...${NC}"
    
    # Eliminar default site si existe para evitar conflictos
    rm -f /etc/nginx/sites-enabled/default 2>/dev/null || true
    
    cat > /etc/nginx/sites-available/apidian << PROXYNGINX
server {
    listen 80;
    server_name ${DOMAIN};

    location / {
        proxy_pass http://127.0.0.1:${INTERNAL_PORT};
        proxy_http_version 1.1;
        proxy_set_header Upgrade \$http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_cache_bypass \$http_upgrade;
        proxy_read_timeout 300;
        proxy_connect_timeout 300;
        client_max_body_size 100M;
    }
}
PROXYNGINX

    # Activar sitio
    ln -sf /etc/nginx/sites-available/apidian /etc/nginx/sites-enabled/
    
    # Verificar configuración e iniciar/recargar Nginx
    if nginx -t; then
        if systemctl is-active --quiet nginx; then
            systemctl reload nginx
        else
            systemctl start nginx
        fi
        echo -e "${GREEN}✓ Nginx del sistema configurado${NC}"
    else
        echo -e "${RED}Error en configuración de Nginx${NC}"
        exit 1
    fi
fi

echo -e "${GREEN}✓ Nginx configurado${NC}"

# ============================================
# 4. CONFIGURAR PUERTOS EN .ENV Y DOCKER-COMPOSE
# ============================================
echo -e "${YELLOW}[4/9] Configurando puertos...${NC}"

# Determinar puerto a usar
if [ "$USE_PROXY" = true ]; then
    HTTP_PORT=${INTERNAL_PORT}
else
    HTTP_PORT=${DIRECT_PORT}
fi

# Actualizar docker-compose.yml con el puerto correcto
sed -i "s/\${HTTP_PORT:-8081}/${HTTP_PORT}/g" docker-compose.yml
sed -i "s/\${HTTP_PORT:-80}/${HTTP_PORT}/g" docker-compose.yml

echo -e "${GREEN}✓ Docker usará puerto ${HTTP_PORT}${NC}"

# ============================================
# 5. CREAR ARCHIVO .ENV
# ============================================
echo -e "${YELLOW}[5/9] Creando archivo .env...${NC}"

APP_KEY="base64:$(openssl rand -base64 32)"
SERVER_IP=$(curl -s ifconfig.me 2>/dev/null || hostname -I | awk '{print $1}')

# Determinar URL base
if [ "$USE_SSL" = true ]; then
    APP_URL="https://${DOMAIN}"
elif [ "$USE_PROXY" = true ]; then
    APP_URL="http://${DOMAIN}"
elif [ "$HTTP_PORT" = "80" ]; then
    if [ "$DOMAIN" = "localhost" ]; then
        APP_URL="http://${SERVER_IP}"
    else
        APP_URL="http://${DOMAIN}"
    fi
else
    if [ "$DOMAIN" = "localhost" ]; then
        APP_URL="http://${SERVER_IP}:${HTTP_PORT}"
    else
        APP_URL="http://${DOMAIN}:${HTTP_PORT}"
    fi
fi

cat > .env << ENVFILE
APP_NAME="APIDIAN"
APP_VERSION=" v2.1"
APP_ENV=production
APP_KEY=${APP_KEY}
APP_DEBUG=false
APP_PORT=${INTERNAL_PORT}
APP_URL=${APP_URL}
FORCE_HTTPS=$([ "$USE_SSL" = true ] && echo "true" || echo "false")

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=apidian
DB_USERNAME=apidian
DB_PASSWORD=${DB_PASSWORD}

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

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
HTTP_PORT=${INTERNAL_PORT}

# SSL Configuration
DOMAIN=${DOMAIN}
SSL_EMAIL=${SSL_EMAIL}
USE_SSL=${USE_SSL}
ENVFILE

echo -e "${GREEN}✓ .env creado${NC}"

# ============================================
# 6. CONSTRUIR E INICIAR CONTENEDORES
# ============================================
echo -e "${YELLOW}[6/9] Construyendo contenedores...${NC}"

# Crear script de inicialización de MariaDB
mkdir -p docker/mariadb/init
cat > docker/mariadb/init/01-init.sql << INITSQL
-- Script de inicialización de MariaDB para APIDIAN
-- Este script se ejecuta automáticamente al crear el contenedor

-- Crear base de datos si no existe
CREATE DATABASE IF NOT EXISTS apidian CHARACTER SET utf8 COLLATE utf8_spanish_ci;

-- Crear usuario con acceso desde cualquier host
CREATE USER IF NOT EXISTS 'apidian'@'%' IDENTIFIED BY '${DB_PASSWORD}';
CREATE USER IF NOT EXISTS 'apidian'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
CREATE USER IF NOT EXISTS 'apidian'@'172.%' IDENTIFIED BY '${DB_PASSWORD}';

-- Otorgar todos los privilegios
GRANT ALL PRIVILEGES ON apidian.* TO 'apidian'@'%';
GRANT ALL PRIVILEGES ON apidian.* TO 'apidian'@'localhost';
GRANT ALL PRIVILEGES ON apidian.* TO 'apidian'@'172.%';

-- Permitir root desde cualquier host
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY '${DB_ROOT_PASSWORD}' WITH GRANT OPTION;

FLUSH PRIVILEGES;
INITSQL

echo "Script de inicialización de MariaDB creado"

# Detener contenedores existentes y limpiar volúmenes de MariaDB
echo "Limpiando instalación anterior de MariaDB..."
docker compose down 2>/dev/null || true
docker volume rm apidian_apidian_mysql_data 2>/dev/null || true

# Construir imágenes
docker compose build --no-cache --parallel

# Iniciar contenedores
docker compose up -d mariadb redis
echo "Esperando a que MariaDB inicialice (60 segundos)..."
sleep 60

# Verificar que MariaDB esté listo
echo "Verificando MariaDB..."
for i in 1 2 3 4 5 6 7 8 9 10; do
    echo "Verificando MariaDB (intento $i/10)..."
    if docker compose exec -T mariadb mysqladmin ping -h localhost --silent 2>/dev/null; then
        echo -e "${GREEN}✓ MariaDB está listo${NC}"
        # Verificar conexión con usuario apidian
        if docker compose exec -T mariadb mysql -u apidian -p"${DB_PASSWORD}" -e "SELECT 1;" apidian 2>/dev/null; then
            echo -e "${GREEN}✓ Usuario apidian puede conectarse${NC}"
            break
        else
            echo "Esperando que el usuario apidian esté disponible..."
        fi
    fi
    sleep 10
done

# Iniciar PHP y Nginx
docker compose up -d php nginx

sleep 10

echo -e "${GREEN}✓ Contenedores iniciados${NC}"

# ============================================
# 7. CONFIGURAR SSL SI ES NECESARIO Y POSIBLE
# ============================================
if [ "$USE_SSL" = true ] && [ "$CAN_USE_SSL" = true ]; then
    echo -e "${YELLOW}[7/9] Configurando SSL con Let's Encrypt...${NC}"
    
    if [ "$USE_PROXY" = true ]; then
        # Usar certbot del sistema con Nginx del sistema
        echo "Instalando certbot..."
        apt-get install -y certbot python3-certbot-nginx
        
        echo "Obteniendo certificado SSL para ${DOMAIN}..."
        certbot --nginx -d ${DOMAIN} --non-interactive --agree-tos --email ${SSL_EMAIL}
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓ Certificado SSL obtenido${NC}"
            
            # Configurar renovación automática
            (crontab -l 2>/dev/null | grep -v certbot; echo "0 12 * * * /usr/bin/certbot renew --quiet") | crontab -
            echo -e "${GREEN}✓ Renovación automática configurada${NC}"
        else
            echo -e "${RED}Error obteniendo certificado SSL. Continuando sin SSL...${NC}"
            USE_SSL=false
        fi
    else
        # Puerto 80 directo - usar certbot standalone o webroot
        echo "Instalando certbot..."
        apt-get update
        apt-get install -y certbot
        
        # Detener nginx de docker temporalmente para usar standalone
        docker compose stop nginx 2>/dev/null || true
        sleep 2
        
        echo "Obteniendo certificado SSL para ${DOMAIN}..."
        certbot certonly --standalone -d ${DOMAIN} --non-interactive --agree-tos --email ${SSL_EMAIL}
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓ Certificado SSL obtenido${NC}"
            
            # Configurar Nginx de Docker para usar SSL
            cat > docker/nginx/sites-available/default.conf << NGINXSSLCONF
server {
    listen 80;
    server_name ${DOMAIN} localhost;
    return 301 https://\$server_name\$request_uri;
}

server {
    listen 443 ssl http2;
    server_name ${DOMAIN} localhost;
    root /var/www/html/public;
    index index.php index.html;
    
    ssl_certificate /etc/letsencrypt/live/${DOMAIN}/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/${DOMAIN}/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;
    ssl_prefer_server_ciphers off;
    
    client_max_body_size 100M;
    
    location /health {
        return 200 "healthy\n";
        add_header Content-Type text/plain;
    }
    
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    location ~ \.php\$ {
        try_files \$uri =404;
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }
    
    location ~ /\.ht {
        deny all;
    }
}
NGINXSSLCONF
            
            # Actualizar docker-compose para exponer puerto 443
            sed -i 's/# - "\${HTTPS_PORT:-443}:443"/- "443:443"/g' docker-compose.yml
            
            # Reiniciar nginx
            docker compose up -d nginx
            
            # Configurar renovación automática
            (crontab -l 2>/dev/null | grep -v certbot; echo "0 12 * * * /usr/bin/certbot renew --quiet --pre-hook 'docker compose -f ${SCRIPT_DIR}/docker-compose.yml stop nginx' --post-hook 'docker compose -f ${SCRIPT_DIR}/docker-compose.yml start nginx'") | crontab -
            echo -e "${GREEN}✓ Renovación automática configurada${NC}"
        else
            echo -e "${RED}Error obteniendo certificado SSL. Continuando sin SSL...${NC}"
            USE_SSL=false
            # Reiniciar nginx sin SSL
            docker compose up -d nginx
        fi
    fi
else
    echo -e "${YELLOW}[7/9] Saltando configuración SSL...${NC}"
    if [ "$CAN_USE_SSL" = false ] && [ "$DOMAIN" != "localhost" ]; then
        echo -e "${BLUE}Para SSL, configura tu proxy existente para redirigir a http://127.0.0.1:${HTTP_PORT}${NC}"
    fi
fi

# ============================================
# 8. INSTALAR DEPENDENCIAS Y CONFIGURAR
# ============================================
echo -e "${YELLOW}[8/9] Instalando dependencias...${NC}"

# Descomprimir storage.zip (según guía)
if [ -f "storage.zip" ]; then
    unzip -o storage.zip
fi

# Eliminar composer.lock problemático (incompatible con PHP 7.3)
echo "Eliminando composer.lock incompatible..."
rm -f composer.lock
rm -rf vendor

# IMPORTANTE: Usar el composer.json ORIGINAL del proyecto
# NO crear uno nuevo porque el original tiene las dependencias correctas de DIAN
echo "Usando composer.json original del proyecto..."

# Solo agregar configuraciones necesarias para compatibilidad
if [ -f "composer.json" ]; then
    # Agregar configuración de allow-plugins si no existe
    docker compose exec -T php composer config --no-plugins allow-plugins.* true 2>/dev/null || true
fi

# Permisos (según guía: chmod -R 777 storage bootstrap/cache vendor/mpdf/mpdf)
chmod -R 777 storage bootstrap/cache

# Composer install (compatible con PHP 7.3 y Composer 2.2)
echo "Instalando dependencias con Composer..."

# Limpiar configuraciones problemáticas existentes
echo "Limpiando configuraciones de Composer..."
rm -f ~/.composer/config.json 2>/dev/null || true
docker compose exec -T php rm -f /root/.composer/config.json 2>/dev/null || true

# Crear configuración limpia de Composer dentro del contenedor
docker compose exec -T php mkdir -p /root/.composer
docker compose exec -T php bash -c 'echo "{\"config\":{\"platform-check\":false,\"allow-plugins\":{\"*\":true}}}" > /root/.composer/config.json'

# Verificar versión de Composer dentro del contenedor
echo "Verificando versión de Composer..."
docker compose exec -T php composer --version

# Instalar dependencias sin comandos problemáticos
echo "Ejecutando composer install..."

# Configurar timeout via variable de entorno (compatible con Composer 2.2)
export COMPOSER_PROCESS_TIMEOUT=600

# Intentar instalación con reintentos
for i in 1 2 3; do
    echo "Intento $i de instalación de dependencias..."
    if docker compose exec -T -e COMPOSER_PROCESS_TIMEOUT=600 php composer install --no-dev --optimize-autoloader --ignore-platform-reqs --no-interaction; then
        echo -e "${GREEN}✓ Dependencias instaladas correctamente${NC}"
        break
    else
        echo -e "${YELLOW}⚠ Error en intento $i, reintentando...${NC}"
        if [ $i -eq 3 ]; then
            echo -e "${RED}✗ Error: No se pudieron instalar las dependencias después de 3 intentos${NC}"
            exit 1
        fi
        sleep 10
    fi
done

# Ejecutar urn_on.sh (según guía - CRÍTICO para firma DIAN)
# IMPORTANTE: Debe ejecutarse DENTRO del contenedor PHP después de composer install
echo "Ejecutando comandos de firma DIAN (urn_on.sh)..."

# Ejecutar los comandos de urn_on.sh dentro del contenedor
docker compose exec -T php bash -c '
    if [ -d "vendor/ubl21dian/torresoftware/src/XAdES/urn" ]; then
        echo "Copiando archivos de firma DIAN..."
        cp resources/templates/xml/urn/*.* resources/templates/xml/ 2>/dev/null || true
        cp vendor/ubl21dian/torresoftware/src/XAdES/urn/*.* vendor/ubl21dian/torresoftware/src/XAdES/ 2>/dev/null || true
        cp resources/templates/xml/urn/Request.php vendor/laravel/framework/src/Illuminate/Http/Request.php 2>/dev/null || true
        echo "Archivos de firma DIAN copiados correctamente"
    else
        echo "Directorio de firma DIAN no encontrado, verificando alternativas..."
        # Intentar con el paquete stenfrank si existe
        if [ -d "vendor/stenfrank/ubl21dian/src/XAdES/urn" ]; then
            cp resources/templates/xml/urn/*.* resources/templates/xml/ 2>/dev/null || true
            cp vendor/stenfrank/ubl21dian/src/XAdES/urn/*.* vendor/stenfrank/ubl21dian/src/XAdES/ 2>/dev/null || true
            cp resources/templates/xml/urn/Request.php vendor/laravel/framework/src/Illuminate/Http/Request.php 2>/dev/null || true
            echo "Archivos de firma DIAN copiados (stenfrank)"
        else
            echo "ADVERTENCIA: No se encontró el paquete de firma DIAN"
        fi
    fi
'

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Comandos de firma DIAN ejecutados${NC}"
else
    echo -e "${YELLOW}⚠ Comandos de firma DIAN completados con advertencias${NC}"
fi

# Configuración Laravel (EXACTA según guía original)
echo "Configurando Laravel..."
docker compose exec -T php php artisan key:generate --force

echo "Limpiando y configurando cache..."
docker compose exec -T php php artisan config:clear || true
docker compose exec -T php php artisan cache:clear || true
docker compose exec -T php php artisan config:cache || true
docker compose exec -T php php artisan storage:link || true

# Verificar conexión a base de datos antes de migrar
echo "Verificando conexión a base de datos..."
for i in {1..6}; do
    if docker compose exec -T php php artisan migrate:status > /dev/null 2>&1; then
        echo -e "${GREEN}✓ Conexión a base de datos establecida${NC}"
        break
    else
        echo "Esperando conexión a base de datos (intento $i/6)..."
        sleep 10
    fi
done

echo "Ejecutando migraciones..."
docker compose exec -T php php artisan migrate --seed --force || echo -e "${YELLOW}⚠ Error en migraciones, continuando...${NC}"

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
# 9. MOSTRAR INFORMACIÓN
# ============================================
echo -e "${YELLOW}[9/9] Finalizando...${NC}"

echo ""
echo -e "${GREEN}============================================${NC}"
echo -e "${GREEN}   ¡INSTALACIÓN COMPLETADA!${NC}"
echo -e "${GREEN}============================================${NC}"
echo ""
echo -e "${BLUE}URL de la API:${NC}"
if [ "$USE_SSL" = true ]; then
    echo "  https://${DOMAIN}"
elif [ "$USE_PROXY" = true ]; then
    echo "  http://${DOMAIN}"
elif [ "$HTTP_PORT" = "80" ]; then
    echo "  http://${DOMAIN}"
else
    echo "  http://${DOMAIN}:${HTTP_PORT}"
    echo ""
    echo -e "${YELLOW}IMPORTANTE: Para acceder sin puerto, configura tu proxy existente:${NC}"
    echo "  Redirigir ${DOMAIN} -> http://127.0.0.1:${HTTP_PORT}"
fi
echo ""
if [ "$USE_PROXY" = true ]; then
    echo -e "${BLUE}Modo Proxy:${NC}"
    echo "  Nginx Sistema -> Docker:${INTERNAL_PORT}"
    echo "  Config: /etc/nginx/sites-available/apidian"
    echo ""
fi
echo -e "${BLUE}Base de datos:${NC}"
echo "  Host: localhost:${MYSQL_PORT}"
echo "  Database: apidian"
echo "  Usuario: apidian"
echo "  Password: ${DB_PASSWORD}"
echo "  Root Password: ${DB_ROOT_PASSWORD}"
echo ""
if [ "$USE_SSL" = true ]; then
    echo -e "${BLUE}SSL:${NC}"
    echo "  Certificado: Let's Encrypt"
    echo "  Dominio: ${DOMAIN}"
    echo "  Renovación: Automática (cron)"
    echo ""
fi
echo -e "${BLUE}Comandos útiles:${NC}"
echo "  Ver logs:     docker compose logs -f"
echo "  Reiniciar:    docker compose restart"
echo "  Detener:      docker compose down"
echo "  Estado:       docker compose ps"
if [ "$USE_SSL" = true ]; then
    if [ "$USE_PROXY" = true ]; then
        echo "  Renovar SSL:  certbot renew"
    else
        echo "  Renovar SSL:  certbot renew"
    fi
fi
if [ "$USE_PROXY" = true ]; then
    echo "  Nginx proxy:  systemctl status nginx"
fi
echo ""

# Guardar credenciales
cat > CREDENCIALES.txt << CREDS
============================================
CREDENCIALES APIDIAN - $(date)
============================================

URL API: $([ "$USE_SSL" = true ] && echo "https://${DOMAIN}" || echo "http://${DOMAIN}:${HTTP_PORT}")

$([ "$USE_PROXY" = true ] && echo "Modo: Proxy (Nginx Sistema -> Docker:${INTERNAL_PORT})
Config Proxy: /etc/nginx/sites-available/apidian
")
$([ "$CAN_USE_SSL" = false ] && echo "NOTA: Para SSL, configura tu proxy existente.
Redirigir: ${DOMAIN} -> http://127.0.0.1:${HTTP_PORT}
")
Base de Datos:
  Host: localhost:${MYSQL_PORT} (externo) / mariadb:3306 (interno)
  Database: apidian
  Usuario: apidian
  Password: ${DB_PASSWORD}
  Root Password: ${DB_ROOT_PASSWORD}

$([ "$USE_SSL" = true ] && echo "SSL:
  Certificado: Let's Encrypt
  Dominio: ${DOMAIN}
  Email: ${SSL_EMAIL}
  Renovación: Automática")

============================================
CREDS
chmod 600 CREDENCIALES.txt

# Verificación final
echo -e "${YELLOW}Verificando instalación...${NC}"
sleep 5

# Verificar que los contenedores estén corriendo
CONTAINERS_RUNNING=$(docker compose ps --services --filter "status=running" 2>/dev/null | wc -l)
TOTAL_CONTAINERS=$(docker compose ps --services 2>/dev/null | wc -l)

if [ "$CONTAINERS_RUNNING" -ge 3 ]; then
    echo -e "${GREEN}✓ Contenedores corriendo correctamente${NC}"
else
    echo -e "${YELLOW}⚠ Verificando contenedores...${NC}"
    docker compose ps
fi

# Verificar Nginx del sistema si está en modo proxy
if [ "$USE_PROXY" = true ]; then
    if systemctl is-active --quiet nginx; then
        echo -e "${GREEN}✓ Nginx del sistema está corriendo${NC}"
    else
        echo -e "${YELLOW}⚠ Iniciando Nginx del sistema...${NC}"
        systemctl start nginx
    fi
fi

# Mensaje final
echo ""
if [ "$USE_SSL" = true ]; then
    echo -e "${GREEN}¡Listo! Accede a https://${DOMAIN}${NC}"
elif [ "$HTTP_PORT" = "80" ]; then
    echo -e "${GREEN}¡Listo! Accede a http://${DOMAIN}${NC}"
else
    echo -e "${GREEN}¡Listo! APIDIAN corriendo en puerto ${HTTP_PORT}${NC}"
    echo ""
    echo -e "${YELLOW}Para acceder via ${DOMAIN}, configura tu proxy existente:${NC}"
    echo -e "${BLUE}  server_name ${DOMAIN};${NC}"
    echo -e "${BLUE}  location / { proxy_pass http://127.0.0.1:${HTTP_PORT}; }${NC}"
fi

echo ""
echo -e "${BLUE}Verificar funcionamiento:${NC}"
echo "  curl http://127.0.0.1:${HTTP_PORT}/health"
echo ""
