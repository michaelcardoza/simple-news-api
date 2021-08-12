<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\NewsController;

Route::prefix('news')->group(function () {
    Route::get('/', [NewsController::class, 'index']);
    Route::get('/search', [NewsController::class, 'search']);
    Route::get('/topics/{topic}', [NewsController::class, 'topics']);
});
