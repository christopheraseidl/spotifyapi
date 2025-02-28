#!/bin/sh

# Establecer permisos.
chown -R www-data:www-data /var/www/html/storage
chmod -R 775 /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/bootstrap/cache

# Conectar storage y public.
php artisan storage:link

# Optimizar Laravel.
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan optimize
php artisan migrate --force

# Ejecutar el comando.
exec "$@"