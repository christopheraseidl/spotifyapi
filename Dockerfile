FROM php:8.4-apache

# Instalar las dependencias del sistema.
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    postgresql-client \
    nodejs \
    npm \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar las extensiones de PHP.
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

# Obtener el Composer más reciente.
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo.
WORKDIR /var/www/html

# Configurar Apache.
RUN echo '<VirtualHost *:80>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Options Indexes FollowSymLinks\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite

# Copiar los archivos de dependencias PHP.
COPY composer.json composer.lock package.json package-lock.json /var/www/html/

# Instalar las dependencias de PHP.
RUN composer install --no-dev --optimize-autoloader

# Instalar las dependencias de Node.js.
RUN npm install && npm run build

# Copiar la aplicación.
COPY . /var/www/html

# Establecer los permisos.
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Optimizar Laravel.
RUN php artisan optimize

# Exponer el puerto 80 para Apache.
EXPOSE 80

# Empezar Apache.
CMD ["apache2-foreground"]