FROM php:8.2-cli

# 1. Instalar dependencias del sistema y drivers de Microsoft
RUN apt-get update && apt-get install -y \
    gnupg2 curl apt-transport-https git unzip libpng-dev libonig-dev libxml2-dev zip \
    && mkdir -p /etc/apt/keyrings \
    && curl https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /etc/apt/keyrings/microsoft.gpg \
    && echo "deb [arch=amd64,arm64,armhf signed-by=/etc/apt/keyrings/microsoft.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools18 unixodbc-dev \
    && pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv

# 2. Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Configurar directorio de trabajo
WORKDIR /var/www
COPY . .

# 4. Instalar dependencias de PHP (Sin ejecutar scripts que requieran base de datos)
RUN composer install --no-dev --no-scripts --optimize-autoloader

# 5. CREACIÓN DE CARPETAS CRÍTICAS (Esto evita el error de 0.15ms)
# Laravel colapsa si estas carpetas no existen físicamente
RUN mkdir -p storage/framework/sessions \
             storage/framework/views \
             storage/framework/cache \
             storage/logs \
             bootstrap/cache

# 6. AJUSTE DE PERMISOS DEFINITIVO
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# 7. Exponer puerto (Railway usa el 80 por defecto)
EXPOSE 80

# 8. COMANDO DE INICIO (Usa la variable $PORT de Railway)
# Limpiamos caché al arrancar para asegurar que tome las variables de Railway y no las de tu PC
CMD php artisan storage:link && php artisan config:clear && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}