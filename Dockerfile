# Usamos la imagen oficial de PHP con FPM
FROM php:8.2-fpm

# Instalar dependencias del sistema y herramientas de Microsoft para SQL Server
RUN apt-get update && apt-get install -y \
    gnupg2 \
    curl \
    apt-transport-https \
    git \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    && mkdir -p /etc/apt/keyrings \
    && curl https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /etc/apt/keyrings/microsoft.gpg \
    && echo "deb [arch=amd64,arm64,armhf signed-by=/etc/apt/keyrings/microsoft.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools18 unixodbc-dev \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar el directorio de trabajo
WORKDIR /var/www

# Copiar el proyecto al contenedor
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader

# Ajustar permisos para Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# --- NUEVO: Generar caché durante la construcción para que no bloquee el inicio ---
# Nota: Esto requiere que tengas un .env temporal o que las variables no sean nulas
RUN php artisan config:cache && php artisan route:cache

# Puerto dinámico asignado por Railway
EXPOSE 80

# --- NUEVO: Comando de inicio simplificado ---
# Eliminamos los '&&' del inicio para que el servidor arranque de inmediato
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-80}