<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Rol
 * Tabla: roles
 *
 * Define los perfiles de acceso del sistema.
 * Actualmente existen dos roles (seeder):
 *   - administrador : acceso total a todas las secciones
 *   - secretaria    : acceso a trabajadores, lotes, actividades y pagos
 *                     (sin acceso a usuarios ni tarifas/tipos de actividad)
 *
 * El middleware CheckRol usa el campo 'nombre' del rol para controlar
 * el acceso a las rutas protegidas.
 *
 * Columnas:
 *   - nombre     : identificador del rol ('administrador', 'secretaria')
 *   - descripcion: descripción del nivel de acceso
 */
class Rol extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Usuarios que tienen este rol asignado.
     */
    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id');
    }
}
