<?php

use App\Http\Controllers\ConcertsController;
use App\Http\Controllers\ConcertsRecordingsController;
use App\Http\Controllers\SongsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('concerts')->group(function () {
    Route::get('all', [ConcertsController::class, 'all']);
    Route::get('upcoming', [ConcertsController::class, 'upcoming']);
    Route::get('past', [ConcertsController::class, 'past']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('download')->group(function () {
        Route::get('recording', [ConcertsRecordingsController::class, 'show']);
        Route::get('recordings', [ConcertsRecordingsController::class, 'index']);
        Route::get('song', [SongsController::class, 'show']);
        Route::get('songs', [SongsController::class, 'index']);
    });
});
