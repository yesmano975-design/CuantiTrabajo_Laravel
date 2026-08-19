<?php

/**
 * routes/web.php
 *
 * Define todas las rutas web del sistema CuantiTrabajo.
 * Las rutas están divididas en dos grupos según el nivel de acceso:
 *
 * ┌─────────────────────────────────────────────────────────────────────┐
 * │ Grupo 1: middleware('auth')                                         │
 * │   Accesible para cualquier usuario autenticado (administrador       │
 * │   y secretaria). Incluye: dashboard, trabajadores, lotes,           │
 * │   actividades y pagos.                                              │
 * ├─────────────────────────────────────────────────────────────────────┤
 * │ Grupo 2: middleware('auth', 'role:administrador')                   │
 * │   Exclusivo del administrador. Incluye: usuarios, tipos de          │
 * │   actividad y tarifas (configuración del catálogo de tarifas).      │
 * └─────────────────────────────────────────────────────────────────────┘
 *
 * El middleware 'role' está definido en bootstrap/app.php como alias
 * de App\Http\Middleware\CheckRol.
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\TrabajadorController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\TipoActividadController;
use App\Http\Controllers\ValorActividadController;
use App\Http\Controllers\ActividadLaboralController;
use App\Http\Controllers\PagoController;

// Página de inicio pública (redirige al login si no hay sesión)
Route::get('/', function () {
    return view('welcome');
});

// ═════════════════════════════════════════════════════════════════════════
// GRUPO 1: Rutas accesibles para administrador y secretaria
// ═════════════════════════════════════════════════════════════════════════
Route::middleware(['auth'])->group(function () {

    // ── Dashboard ──────────────────────────────────────────────────────
    // Muestra el resumen general del sistema (contadores de tarjetas).
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Trabajadores ───────────────────────────────────────────────────
    // CRUD completo de trabajadores del campo.
    // La ruta toggle-estado alterna activo/inactivo sin entrar al formulario.
    Route::resource('trabajadores', TrabajadorController::class)
        ->parameters(['trabajadores' => 'trabajador']);
    Route::patch('trabajadores/{trabajador}/toggle-estado', [TrabajadorController::class, 'toggleEstado'])
        ->name('trabajadores.toggleEstado');

    // ── Lotes ──────────────────────────────────────────────────────────
    // CRUD completo de lotes o parcelas de la finca.
    Route::resource('lotes', LoteController::class);

    // ── Actividades Laborales ──────────────────────────────────────────
    // CRUD del registro diario de labores.
    // La ruta 'confirmar' cambia el estado_confirmacion de una actividad
    // (pendiente → confirmado | rechazado), habilitando su inclusión en pagos.
    Route::resource('actividades', ActividadLaboralController::class)
        ->names('actividades')
        ->parameters(['actividades' => 'actividad']);
    Route::patch('actividades/{actividad}/confirmar', [ActividadLaboralController::class, 'confirmar'])
        ->name('actividades.confirmar');

    // ── Pagos / Liquidaciones ──────────────────────────────────────────
    // Gestión de liquidaciones semanales.
    // La ruta 'marcar-pagado' cambia el estado de un pago de pendiente a pagado.
    Route::resource('pagos', PagoController::class);
    Route::patch('pagos/{pago}/marcar-pagado', [PagoController::class, 'marcarPagado'])
        ->name('pagos.marcarPagado');

});

// ═════════════════════════════════════════════════════════════════════════
// GRUPO 2: Rutas exclusivas del administrador
// El middleware 'role:administrador' rechaza con 403 a la secretaria.
// ═════════════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:administrador'])->group(function () {

    // ── Usuarios del sistema ───────────────────────────────────────────
    // Gestión de cuentas de acceso al panel (crear admins y secretarias).
    // La ruta toggle-estado activa o desactiva una cuenta de usuario.
    Route::resource('usuarios', UsuarioController::class);
    Route::patch('usuarios/{usuario}/toggle-estado', [UsuarioController::class, 'toggleEstado'])
        ->name('usuarios.toggleEstado');

    // ── Catálogo: Tipos de Actividad ───────────────────────────────────
    // Define las categorías de labor (Fumigación, Poda, etc.) y su
    // unidad de medida. Base para crear las tarifas.
    Route::resource('tipo-actividades', TipoActividadController::class)
        ->names('tipo-actividades')
        ->parameters(['tipo-actividades' => 'tipo_actividad']);

    // ── Catálogo: Tarifas (ValorActividad) ─────────────────────────────
    // Define el valor económico por unidad de cada tipo de actividad
    // para un rango de fechas. Permite historial de precios.
    Route::resource('tarifas', ValorActividadController::class)
        ->names('tarifas');

});

require __DIR__.'/auth.php';
