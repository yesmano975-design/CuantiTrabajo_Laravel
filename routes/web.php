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
Route::middleware(['auth', 'no-cache'])->group(function () {

    // ── Dashboard ──────────────────────────────────────────────────────
    // Muestra el resumen general del sistema (contadores de tarjetas).
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Trabajadores ───────────────────────────────────────────────────
    // CRUD completo. Los formularios son modales inline en trabajadores/index.
    Route::resource('trabajadores', TrabajadorController::class)
        ->parameters(['trabajadores' => 'trabajador'])
        ->except(['create', 'edit', 'show']);
    Route::patch('trabajadores/{trabajador}/toggle-estado', [TrabajadorController::class, 'toggleEstado'])
        ->name('trabajadores.toggleEstado');

    // ── Lotes ──────────────────────────────────────────────────────────
    // CRUD completo. Los formularios son modales inline en lotes/index.
    Route::resource('lotes', LoteController::class)
        ->except(['create', 'edit', 'show']);

    // ── Actividades Laborales ──────────────────────────────────────────
    // CRUD del registro diario de labores.
    // La ruta 'confirmar' cambia el estado_confirmacion de una actividad
    // (pendiente → confirmado | rechazado), habilitando su inclusión en pagos.
    Route::resource('actividades', ActividadLaboralController::class)
        ->names('actividades')
        ->parameters(['actividades' => 'actividad'])
        ->except(['create', 'edit']);
    Route::patch('actividades/{actividad}/confirmar', [ActividadLaboralController::class, 'confirmar'])
        ->name('actividades.confirmar');
    Route::get('actividades/avance-lote', [ActividadLaboralController::class, 'avanceLote'])
        ->name('actividades.avanceLote');

    // ── Pagos / Liquidaciones ──────────────────────────────────────────
    // Gestión de liquidaciones semanales.
    // La ruta 'marcar-pagado' cambia el estado de un pago de pendiente a pagado.
    Route::resource('pagos', PagoController::class);
    
    Route::patch('pagos/{pago}/marcar-pagado', [PagoController::class, 'marcarPagado'])
        ->name('pagos.marcarPagado');

    Route::patch('pagos/{pago}/agregar-actividades', [PagoController::class, 'agregarActividades'])
        ->name('pagos.agregarActividades');

});

// ═════════════════════════════════════════════════════════════════════════
// GRUPO 2: Rutas exclusivas del administrador
// El middleware 'role:administrador' rechaza con 403 a la secretaria.
// ═════════════════════════════════════════════════════════════════════════
Route::middleware(['auth', 'role:administrador', 'no-cache'])->group(function () {

    // ── Usuarios del sistema ───────────────────────────────────────────
    // CRUD completo. Los formularios son modales inline en usuarios/index.
    Route::resource('usuarios', UsuarioController::class)
        ->except(['create', 'edit', 'show']);
    Route::patch('usuarios/{usuario}/toggle-estado', [UsuarioController::class, 'toggleEstado'])
        ->name('usuarios.toggleEstado');

    // ── Catálogo: Tipos de Actividad ───────────────────────────────────
    // Los formularios son modales inline en tipo-actividades/index.
    Route::resource('tipo-actividades', TipoActividadController::class)
        ->names('tipo-actividades')
        ->parameters(['tipo-actividades' => 'tipo_actividad'])
        ->except(['create', 'edit', 'show']);

    // ── Catálogo: Tarifas (ValorActividad) ─────────────────────────────
    // Los formularios son modales inline en tarifas/index.
    Route::resource('tarifas', ValorActividadController::class)
        ->names('tarifas')
        ->except(['create', 'edit', 'show']);

});

require __DIR__.'/auth.php';
