<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\SolicitudRegistroController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Middleware\EsAdministrador;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EstimacionPesoController;

/*
|--------------------------------------------------------------------------
| Módulo 1 — Gestión de Usuarios y Autenticación
|--------------------------------------------------------------------------
|
| Rutas públicas:  Login, recuperación de contraseña, envío de solicitud.
| Rutas protegidas (auth:sanctum): logout, perfil.
| Rutas de admin (auth:sanctum + EsAdministrador): CRUD usuarios y solicitudes.
|
*/

// ── Rutas públicas ────────────────────────────────────────────────────────────
Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
});

// Solicitud de registro (pública — el solicitante aún no tiene cuenta)
Route::post('/solicitudes', [SolicitudRegistroController::class, 'store'])->name('solicitudes.store');

// ── Rutas protegidas (usuario autenticado) ────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
    Route::get('/auth/me', [AuthController::class, 'me'])->name('auth.me');

    // ── Rutas de administración (requiere rol Administrador) ──────────────────
    Route::middleware(EsAdministrador::class)->group(function () {

        // CRUD de usuarios (HU-01.4 a HU-01.7)
        Route::apiResource('usuarios', UsuarioController::class)
            ->only(['index', 'show', 'store', 'update', 'destroy']);

        // Gestión de solicitudes de registro (HU-01.8)
        Route::get('/solicitudes', [SolicitudRegistroController::class, 'index'])->name('solicitudes.index');
        Route::get('/solicitudes/pendientes', [SolicitudRegistroController::class, 'pendientes'])->name('solicitudes.pendientes');
        Route::get('/solicitudes/{id}', [SolicitudRegistroController::class, 'show'])->name('solicitudes.show');
        Route::put('/solicitudes/{id}/revisar', [SolicitudRegistroController::class, 'revisar'])->name('solicitudes.revisar');
    });

    //Rutas de Estimacion de peso
    Route::prefix('estimacion')->group(function () {
    Route::get('/health', [EstimacionPesoController::class, 'healthCheck']);
    Route::post('/estimar', [EstimacionPesoController::class, 'estimar']);
    Route::post('/estimar-batch', [EstimacionPesoController::class, 'estimarBatch']);
});
});



