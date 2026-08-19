<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\TipoActividadController;
use App\Http\Controllers\ValorActividadController;
use App\Http\Controllers\ActividadLaboralController;
use App\Http\Controllers\PagoController;

// Página de inicio
Route::get('/', function () {
    return view('welcome');
});

// Rutas autenticadas
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Usuarios
    Route::resource('usuarios', UsuarioController::class);
    Route::patch('usuarios/{usuario}/toggle-estado', [UsuarioController::class, 'toggleEstado'])
        ->name('usuarios.toggleEstado');

    // Trabajadores
    Route::resource('trabajadores', TrabajadorController::class)
        ->parameters(['trabajadores' => 'trabajador']);
    Route::patch('trabajadores/{trabajador}/toggle-estado', [TrabajadorController::class, 'toggleEstado'])
        ->name('trabajadores.toggleEstado');

    // Lotes
    Route::resource('lotes', LoteController::class);

    // Tipo de Actividades
    Route::resource('tipo-actividades', TipoActividadController::class)
        ->names('tipo-actividades')
        ->parameters(['tipo-actividades' => 'tipo_actividad']);

    // Tarifas (ValorActividad)
    Route::resource('tarifas', ValorActividadController::class)
        ->names('tarifas');

    // Actividades Laborales
    Route::resource('actividades', ActividadLaboralController::class)
        ->names('actividades')
        ->parameters(['actividades' => 'actividad']);

    // Confirmar / Rechazar actividad
    Route::patch('actividades/{actividad}/confirmar', [ActividadLaboralController::class, 'confirmar'])
        ->name('actividades.confirmar');

    // Pagos
    Route::resource('pagos', PagoController::class);

    // Ruta extra para marcar pago como pagado
    Route::patch('pagos/{pago}/marcar-pagado', [PagoController::class, 'marcarPagado'])
        ->name('pagos.marcarPagado');

});

require __DIR__.'/auth.php';
