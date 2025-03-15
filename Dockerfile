# Usar la imagen oficial de PHP 8.1 con Apache
FROM php:8.1-apache

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd \
    && docker-php-ext-install gd pdo pdo_mysql mysqli

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf

# Crear carpetas antes de cambiar permisos
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Habilitar el módulo de reescritura de Apache
RUN a2enmod rewrite

# Exponer el puerto 80
EXPOSE 80

# Comando por defecto al iniciar el contenedor
CMD ["apache2-foreground"]
