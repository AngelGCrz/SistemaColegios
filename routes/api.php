<?php

use App\Http\Controllers\Api\V1\AlumnoApiController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DocenteApiController;
use App\Http\Controllers\Api\V1\PadreApiController;
use App\Http\Controllers\Api\V1\ReporteApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (prefix: /api/v1)
|--------------------------------------------------------------------------
*/

// Autenticación
Route::post('/auth/login', [AuthController::class, 'login']);

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Alumno
    Route::middleware('role:alumno')->prefix('alumno')->group(function () {
        Route::get('/dashboard', [AlumnoApiController::class, 'dashboard']);
        Route::get('/notas', [AlumnoApiController::class, 'notas']);
        Route::get('/asistencia', [AlumnoApiController::class, 'asistencia']);
        Route::get('/tareas', [AlumnoApiController::class, 'tareas']);
        Route::get('/pagos', [AlumnoApiController::class, 'pagos']);
    });

    // Padre
    Route::middleware('role:padre')->prefix('padre')->group(function () {
        Route::get('/hijos', [PadreApiController::class, 'hijos']);
        Route::get('/hijos/{alumno}/notas', [PadreApiController::class, 'notasHijo']);
        Route::get('/hijos/{alumno}/asistencia', [PadreApiController::class, 'asistenciaHijo']);
        Route::get('/hijos/{alumno}/pagos', [PadreApiController::class, 'pagosHijo']);
    });

    // Docente
    Route::middleware('role:docente')->prefix('docente')->group(function () {
        Route::get('/cursos', [DocenteApiController::class, 'misCursos']);
        Route::get('/cursos/{cursoSeccion}/tareas', [DocenteApiController::class, 'tareas']);
    });

    // Reportes (admin)
    Route::middleware('role:admin')->prefix('reportes')->group(function () {
        Route::get('/notas-por-curso', [ReporteApiController::class, 'notasPorCurso']);
        Route::get('/asistencia-mensual', [ReporteApiController::class, 'asistenciaMensual']);
        Route::get('/pagos-mensual', [ReporteApiController::class, 'pagosMensual']);
        Route::get('/matriculas-por-nivel', [ReporteApiController::class, 'matriculasPorNivel']);
        Route::get('/rendimiento-general', [ReporteApiController::class, 'rendimientoGeneral']);
    });
});
