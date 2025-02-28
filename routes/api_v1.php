<?php

use App\Http\Controllers\Api\V1\SearchController;
use Illuminate\Support\Facades\Route;

Route::post('/search', [SearchController::class, 'index']);
