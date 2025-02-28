<?php

namespace App\Http\Controllers\Api\V1;

use App\Contracts\SpotifyService;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function __construct(
        protected SpotifyService $spotify
    ) {}
}
