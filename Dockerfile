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
    && curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl https://packages.microsoft.com/config/debian/11/prod.list > /etc/apt/sources.list.d/mssql-release.list \
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

# Puerto que usará Render
EXPOSE 80

# Comando para iniciar la aplicación
CMD php artisan serve --host=0.0.0.0 --port=80