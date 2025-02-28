<?php

namespace App\Contracts;

interface SpotifyService
{
    /**
     * Recuperar o generar token.
     */
    public function getAccessToken(): string;

    /**
     * Enviar un request a la API de Spotify.
     */
    public function apiRequest(string $endpoint, string $method = 'GET', array $params = []): array;

    /**
     * Buscar un recurso según el query.
     */
    public function search(string $query, string $type, int $limit = 10, int $offset = 0): array;
}
