# Una API para obtener información de Spotify.

Spotify API permite acceder a información de Spotify dentro de Laravel.

## Versiones PHP

- PHP 8.4

## Instalación

Se puede instalar este paquete via Composer. Primero, añadir el repositorio al archivo composer.json:
```
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/christopheraseidl/spotifyapi"
        }
    ]
}
```

Después, se puede requirir el paquete:
```bash
composer require christopheraseidl/spotifyapi
```

## Configuración de Docker

1. Copiar .env.example a .env: `cp .env.example .env`
2. Ejecutar `docker compose up -d` para iniciar el contenedor. (Este comando puede variar según la versión local de Docker.)
3. Ejecutar `docker compose exec app php artisan key:generate` para generar la clave de la aplicación.
4. Ejecutar `docker compose exec app php artisan migrate` para crear las tablas de la base de datos.
5. Acceder a la API en http://localhost:8000.

## Licencia

Este paquete es software de código abierto bajo licencia [MIT license](https://opensource.org/licenses/MIT).
