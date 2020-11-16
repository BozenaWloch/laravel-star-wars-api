<?php
declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilmsController;
use App\Http\Controllers\PlanetsController;
use App\Http\Controllers\SpeciesController;
use App\Http\Controllers\StarshipsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VehiclesController;
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

Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

Route::prefix('password')->group(function (): void {
    Route::post('reset', [AuthController::class, 'resetPassword']);
    Route::post('update', [AuthController::class, 'updatePassword']);
});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('logout', [AuthController::class, 'logout']);

    Route::prefix('users')->group(function (): void {
        Route::post('/admin/create', [UsersController::class, 'createAdmin']);
        Route::patch('/{userId}', [UsersController::class, 'update']);
        Route::get('/{userId}', [UsersController::class, 'read']);
        Route::delete('/{userId}', [UsersController::class, 'delete']);

        Route::prefix('{userId}/films')->group(function (): void {
            Route::get('', [FilmsController::class, 'list']);
            Route::get('/{filmId}', [FilmsController::class, 'read']);
        });

        Route::prefix('{userId}/species')->group(function (): void {
            Route::get('', [SpeciesController::class, 'list']);
            Route::get('/{specieId}', [SpeciesController::class, 'read']);
        });

        Route::prefix('{userId}/vehicles')->group(function (): void {
            Route::get('', [VehiclesController::class, 'list']);
            Route::get('/{vehicleId}', [VehiclesController::class, 'read']);
        });

        Route::prefix('{userId}/starships')->group(function (): void {
            Route::get('', [StarshipsController::class, 'list']);
            Route::get('/{starshipId}', [StarshipsController::class, 'read']);
        });

        Route::prefix('{userId}/planets')->group(function (): void {
            Route::get('', [PlanetsController::class, 'list']);
            Route::get('/{planetId}', [PlanetsController::class, 'read']);
        });
    });
});
