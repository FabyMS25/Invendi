<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuUserController;
use App\Http\Controllers\MenuRolController;

// Usuario Routes
Route::apiResource('usuarios', UserController::class);
Route::patch('usuarios/{id}/status', [UserController::class, 'updateStatus']);
Route::post('usuarios/{id}/remember', [UserController::class, 'rememberMe']);

// Public routes
Route::post('login', [AuthController::class, 'login']);
Route::post('forgot-password', [PasswordResetController::class, 'forgot']);
Route::post('reset-password', [PasswordResetController::class, 'reset']);
// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::put('/', [ProfileController::class, 'update']);
        Route::post('password', [ProfileController::class, 'updatePassword']);
    });
});
// Empresa Routes
Route::apiResource('empresas', CompanyController::class);

// Agencia Routes
Route::apiResource('agencias', AgencyController::class);

// Menu Routes
Route::apiResource('menus', MenuController::class);

// MenuUsuario Routes
Route::apiResource('menu-usuarios', MenuUserController::class);

// RolMenu Routes
Route::apiResource('rol-menus', MenuRolController::class);
