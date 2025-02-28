<?php

namespace App\Services;

use App\Contracts\SpotifyService as SpotifyServiceContract;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpotifyService implements SpotifyServiceContract
{
    protected string $baseUrl = 'https://api.spotify.com/v1';

    protected string $authUrl = 'https://accounts.spotify.com/api/token';

    protected string $accessToken;

    public function __construct(
        protected string $clientId,
        protected string $clientSecret
    ) {
        // Obtener el token una vez instanciado el servicio.
        $this->getAccessToken();
    }

    /**
     * Recuperar o generar token.
     */
    public function getAccessToken(): string
    {
        if (Cache::has('spotify_token')) {
            $this->accessToken = Cache::get('spotify_token');

            return $this->accessToken;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic '.base64_encode($this->clientId.':'.$this->clientSecret),
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post($this->authUrl, [
                'grant_type' => 'client_credentials',
            ]);

            $response->throw();
            $data = $response->json();

            $this->accessToken = $data['access_token'];

            // Guardar el valor en el cache.
            $expiresIn = $data['expires_in'] - 60;
            Cache::put('spotify_token', $this->accessToken, $expiresIn);

            return $this->accessToken;
        } catch (RequestException $e) {
            Log::error('Spotify authentication error: '.$e->getMessage());
            throw new \Exception('Failed to authenticate with Spotify API: '.$e->getMessage());
        }
    }

    /**
     * Enviar un request a la API de Spotify.
     */
    public function apiRequest(string $endpoint, string $method = 'GET', array $params = []): array
    {
        try {
            $request = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->accessToken,
                'Content-Type' => 'application/json',
            ]);

            $url = $this->baseUrl.$endpoint;

            $response = match (strtoupper($method)) {
                'GET' => $request->get($url, $params),
                'POST' => $request->post($url, $params),
                'PUT' => $request->put($url, $params),
                'DELETE' => $request->delete($url, $params),
                default => throw new \Exception('Unsupported HTTP method: '.$method)
            };

            $response->throw();

            return $response->json();
        } catch (RequestException $e) {
            // Intentar regenerar el token si se ha caducado.
            if ($e->response->status() === 401) {
                Cache::forget('spotify_token');
                $this->getAccessToken();

                return $this->apiRequest($endpoint, $method, $params);
            }

            Log::error('Spotify request error: '.$e->getMessage());
            throw new \Exception('Spotify API request failed: '.$e->getMessage());
        }
    }

    /**
     * Buscar un recurso según el query.
     */
    public function search(string $query, string $type, int $limit = 10, int $offset = 0): array
    {
        return $this->apiRequest('/search', 'GET', [
            'q' => $query,
            'type' => $type,
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }
}
