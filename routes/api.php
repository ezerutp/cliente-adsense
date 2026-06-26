<?php

use App\Http\Controllers\Api\VideoPostIngestController;
use Illuminate\Support\Facades\Route;

Route::post('/video-posts', VideoPostIngestController::class)
    ->middleware('throttle:10,1')
    ->name('api.video-posts.store');
