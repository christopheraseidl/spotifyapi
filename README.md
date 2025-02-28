# Una API para obtener información de Spotify.

Spotify API permite acceder a información de Spotify dentro de Laravel.

## Versiones PHP

- PHP 8.4+

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
2. Ejecutar `php artisan key:generate` para generar la clave de la aplicación.
3. Ejecutar `docker compose up -d` para iniciar el contenedor. (Este comando puede variar según la versión local de Docker.)
4. Acceder a la API en sus puntos finales.

## Uso

Los usuarios pueden:
- registrar en `/api/register`.
- generar un token de autorización en `/api/login`.
- eliminar el token de autorización en `/api/logout`.

Una vez registrado, los usuarios podrán hacer búsquedas en Spotify a través de peticiones POST en el punto final en `/api/v1/search`.

Cada petición debe contener en sus Headers el token de autorización generado durante el registro o cuando el usuario entre en su cuenta.

### Parámetros de búsqueda

La búsqueda requiere los siguientes parámetros:

- q (requerido): Término de búsqueda para encontrar canciones, artistas, etc.
- type (requerido): Tipo de búsqueda (track, artist, album, playlist)
- page (opcional): Número de página para resultados paginados (por defecto: 1)
- limit (opcional): Número de resultados por página (por defecto: 20, máximo: 50)

Ejemplo de petición POST:
```
POST /api/v1/search
Headers:
  Authorization: Bearer {your_token}
  Content-Type: application/json

Body:
{
  "q": "Wu-Tang Clan",
  "type": "artist",
  "page": 1,
  "limit": 20
}
```

## Documentación

Consulta la documentación interactiva, disponible en el enlace `/docs/api`, para más información detallada sobre todos los puntos finales, parámetros y respuestas de la API.

## Licencia

Este paquete es software de código abierto bajo licencia [MIT license](https://opensource.org/licenses/MIT).
