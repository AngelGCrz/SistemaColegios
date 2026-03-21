<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\Docente;
use App\Http\Controllers\Alumno;
use App\Http\Controllers\Padre;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\BoletaController;
use App\Http\Controllers\ArchivoController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas Autenticadas (colegio activo)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'colegio.activo'])->group(function () {

    // Descarga de archivos (tareas y entregas)
    Route::get('/archivo/{tipo}/{id}', [ArchivoController::class, 'descargar'])
        ->name('archivo.descargar')
        ->where('tipo', 'tarea|entrega')
        ->where('id', '[0-9]+');

    // ===============================================
    // ADMIN
    // ===============================================
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Usuarios CRUD
        Route::resource('usuarios', Admin\UsuarioController::class)->except(['show']);

        // Periodos
        Route::resource('periodos', Admin\PeriodoController::class)->except(['show', 'destroy']);

        // Académico
        Route::get('/academico/niveles', [Admin\AcademicoController::class, 'niveles'])->name('academico.niveles');
        Route::post('/academico/niveles', [Admin\AcademicoController::class, 'storeNivel'])->name('academico.niveles.store');
        Route::post('/academico/grados', [Admin\AcademicoController::class, 'storeGrado'])->name('academico.grados.store');

        Route::get('/academico/secciones', [Admin\AcademicoController::class, 'secciones'])->name('academico.secciones');
        Route::post('/academico/secciones', [Admin\AcademicoController::class, 'storeSeccion'])->name('academico.secciones.store');

        Route::get('/academico/cursos', [Admin\AcademicoController::class, 'cursos'])->name('academico.cursos');
        Route::post('/academico/cursos', [Admin\AcademicoController::class, 'storeCurso'])->name('academico.cursos.store');

        Route::get('/academico/asignaciones', [Admin\AcademicoController::class, 'asignaciones'])->name('academico.asignaciones');
        Route::post('/academico/asignaciones', [Admin\AcademicoController::class, 'storeAsignacion'])->name('academico.asignaciones.store');

        // Matrículas
        Route::get('/matriculas', [Admin\MatriculaController::class, 'index'])->name('matriculas.index');
        Route::get('/matriculas/create', [Admin\MatriculaController::class, 'create'])->name('matriculas.create');
        Route::post('/matriculas', [Admin\MatriculaController::class, 'store'])->name('matriculas.store');
        Route::patch('/matriculas/{matricula}/estado', [Admin\MatriculaController::class, 'updateEstado'])->name('matriculas.estado');

        // Pagos
        Route::get('/pagos', [Admin\PagoController::class, 'index'])->name('pagos.index');
        Route::get('/pagos/create', [Admin\PagoController::class, 'create'])->name('pagos.create');
        Route::post('/pagos', [Admin\PagoController::class, 'store'])->name('pagos.store');
        Route::patch('/pagos/{pago}/pagado', [Admin\PagoController::class, 'marcarPagado'])->name('pagos.pagado');
        Route::get('/pagos/conceptos', [Admin\PagoController::class, 'conceptos'])->name('pagos.conceptos');
        Route::post('/pagos/conceptos', [Admin\PagoController::class, 'storeConcepto'])->name('pagos.conceptos.store');
        Route::get('/pagos/alumno/{alumno}', [Admin\PagoController::class, 'estadoCuenta'])->name('pagos.estado-cuenta');

        // Avisos
        Route::get('/avisos', [Admin\AvisoController::class, 'index'])->name('avisos.index');
        Route::get('/avisos/create', [Admin\AvisoController::class, 'create'])->name('avisos.create');
        Route::post('/avisos', [Admin\AvisoController::class, 'store'])->name('avisos.store');
        Route::delete('/avisos/{aviso}', [Admin\AvisoController::class, 'destroy'])->name('avisos.destroy');
    });

    // ===============================================
    // DOCENTE
    // ===============================================
    Route::middleware('role:docente')->prefix('docente')->name('docente.')->group(function () {

        Route::get('/dashboard', [Docente\DashboardController::class, 'index'])->name('dashboard');

        // Notas
        Route::get('/notas', [Docente\NotaController::class, 'seleccionar'])->name('notas.seleccionar');
        Route::get('/notas/{cursoSeccion}/{bimestre}', [Docente\NotaController::class, 'planilla'])->name('notas.planilla');
        Route::post('/notas/{cursoSeccion}/{bimestre}', [Docente\NotaController::class, 'guardar'])->name('notas.guardar');

        // Asistencia
        Route::get('/asistencia', [Docente\AsistenciaController::class, 'seleccionar'])->name('asistencia.seleccionar');
        Route::post('/asistencia/registrar', [Docente\AsistenciaController::class, 'registrar'])->name('asistencia.registrar');
        Route::post('/asistencia/guardar', [Docente\AsistenciaController::class, 'guardar'])->name('asistencia.guardar');

        // Tareas
        Route::get('/tareas/{cursoSeccion}', [Docente\TareaController::class, 'index'])->name('tareas.index');
        Route::get('/tareas/{cursoSeccion}/create', [Docente\TareaController::class, 'create'])->name('tareas.create');
        Route::post('/tareas/{cursoSeccion}', [Docente\TareaController::class, 'store'])->name('tareas.store');
        Route::get('/tareas/{cursoSeccion}/{tarea}/edit', [Docente\TareaController::class, 'edit'])->name('tareas.edit');
        Route::put('/tareas/{cursoSeccion}/{tarea}', [Docente\TareaController::class, 'update'])->name('tareas.update');
        Route::delete('/tareas/{cursoSeccion}/{tarea}', [Docente\TareaController::class, 'destroy'])->name('tareas.destroy');
        Route::patch('/tareas/{cursoSeccion}/{tarea}/publicar', [Docente\TareaController::class, 'togglePublicada'])->name('tareas.publicar');
        Route::get('/tareas/entregas/{tarea}', [Docente\TareaController::class, 'entregas'])->name('tareas.entregas');
        Route::post('/tareas/calificar/{tarea}', [Docente\TareaController::class, 'calificarTodas'])->name('tareas.calificar');
    });

    // ===============================================
    // ALUMNO
    // ===============================================
    Route::middleware('role:alumno')->prefix('alumno')->name('alumno.')->group(function () {

        Route::get('/dashboard', [Alumno\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/notas', [Alumno\DashboardController::class, 'notas'])->name('notas');
        Route::get('/tareas', [Alumno\DashboardController::class, 'tareas'])->name('tareas');
        Route::post('/tareas/{tarea}/entregar', [Alumno\DashboardController::class, 'entregarTarea'])->name('tareas.entregar');
        Route::get('/calendario', [Alumno\DashboardController::class, 'calendario'])->name('calendario');
        Route::get('/historial', [Alumno\DashboardController::class, 'historial'])->name('historial');
    });

    // ===============================================
    // PADRE
    // ===============================================
    Route::middleware('role:padre')->prefix('padre')->name('padre.')->group(function () {

        Route::get('/dashboard', [Padre\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/notas/{alumno}', [Padre\DashboardController::class, 'notasHijo'])->name('notas');
        Route::get('/asistencia/{alumno}', [Padre\DashboardController::class, 'asistenciaHijo'])->name('asistencia');
        Route::get('/pagos/{alumno}', [Padre\DashboardController::class, 'pagosHijo'])->name('pagos');
    });

    // ===============================================
    // COMPARTIDAS (todos los roles autenticados)
    // ===============================================

    // Mensajes
    Route::get('/mensajes', [MensajeController::class, 'inbox'])->name('mensajes.inbox');
    Route::get('/mensajes/enviados', [MensajeController::class, 'enviados'])->name('mensajes.enviados');
    Route::get('/mensajes/crear', [MensajeController::class, 'create'])->name('mensajes.create');
    Route::post('/mensajes', [MensajeController::class, 'store'])->name('mensajes.store');
    Route::get('/mensajes/{mensaje}', [MensajeController::class, 'show'])->name('mensajes.show');

    // Boleta PDF
    Route::get('/boleta/{matricula}/pdf', [BoletaController::class, 'descargar'])->name('boleta.pdf');
});
