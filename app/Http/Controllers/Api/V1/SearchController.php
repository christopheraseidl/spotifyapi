<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\SearchRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class SearchController extends ApiController
{
    public function index(SearchRequest $request): JsonResponse
    {
        $request->validated();
        $limit = $this->limit($request);
        $page = $this->page($request);
        $offset = $this->offset($request);

        try {
            // Hacer el request a la API Spotify.
            $results = $this->spotify->search(
                $request->q,
                $request->type,
                $limit,
                $offset
            );

            // Obtener los artículos basados según el tipo buscado.
            $type = Str::plural($request->type);
            $items = $results[$type]['items'] ?? [];
            $total = $results[$type]['total'] ?? 0;

            // Crear un Laravel paginator.
            $paginator = new LengthAwarePaginator(
                $items,
                $total,
                $limit,
                $page,
                [
                    'path' => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return response()->json($paginator);
        } catch (\Exception $e) {
            return response()->json(['Spotify API error: '.$e->getMessage()], 500);
        }
    }

    protected function page(SearchRequest $request): int
    {
        return $request->input('page', 1);
    }

    protected function limit(SearchRequest $request): int
    {
        return $request->input('limit', 10);
    }

    protected function offset(SearchRequest $request): int
    {
        $limit = $this->limit($request);
        $page = $this->page($request);

        return ($page - 1) * $limit;
    }
}
