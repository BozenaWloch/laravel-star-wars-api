<?php
declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FilmsController;
use App\Http\Controllers\UserController;
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

Route::prefix('public')->group(function (): void {

});

Route::middleware('auth:sanctum')->group(function (): void {
    Route::get('logout', [AuthController::class, 'logout']);

    Route::prefix('users')->group(function (): void {
        Route::post('/admin/create', [UserController::class, 'createAdmin']);
        Route::patch('/{userId}', [UserController::class, 'update']);
        Route::get('/{userId}', [UserController::class, 'read']);
        Route::delete('/{userId}', [UserController::class, 'delete']);

        Route::prefix('{userId}/films')->group(function (): void {
            Route::get('', [FilmsController::class, 'list']);
            Route::get('/{filmId}', [FilmsController::class, 'read']);
        });
    });
});
