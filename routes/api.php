<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConcertsController;
use App\Http\Controllers\ConcertsRecordingsController;
use App\Http\Controllers\SongsController;
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

Route::prefix('concerts')->group(function () {
    Route::get('all', [ConcertsController::class, 'all']);
    Route::get('upcoming', [ConcertsController::class, 'upcoming']);
    Route::get('past', [ConcertsController::class, 'past']);
});

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'download'], function () {
    Route::get('recording', [ConcertsRecordingsController::class, 'show']);
    Route::get('recordings', [ConcertsRecordingsController::class, 'index']);
    Route::get('song', [SongsController::class, 'show']);
    Route::get('songs', [SongsController::class, 'index']);
});
