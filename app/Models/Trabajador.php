<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Trabajador
 * Tabla: trabajadores
 *
 * Representa a las personas que realizan las labores en el campo.
 * Cada trabajador pertenece a un cargo y puede estar activo o inactivo.
 * Solo los trabajadores activos aparecen disponibles al registrar actividades.
 *
 * Columnas:
 *   - cargo_id  : FK → cargos (función que desempeña en la finca)
 *   - nombre    : primer nombre del trabajador
 *   - apellido  : apellido (opcional)
 *   - documento : número de cédula o documento de identidad (único)
 *   - correo    : correo electrónico de contacto (opcional)
 *   - telefono  : número de teléfono (opcional)
 *   - estado    : 'activo' | 'inactivo'
 *   - huella    : campo reservado para futura integración biométrica
 */
class Trabajador extends Model
{
    protected $table = 'trabajadores';

    protected $fillable = [
        'cargo_id',
        'nombre',
        'apellido',
        'documento',
        'correo',
        'telefono',
        'estado',
        'huella',
    ];

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Cargo asignado al trabajador (Tractorista, Fumigador, etc.).
     */
    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    /**
     * Actividades laborales registradas para este trabajador.
     * Se usa para verificar si puede eliminarse
     * (bloqueado si tiene actividades asociadas).
     */
    public function actividadesLaborales()
    {
        return $this->hasMany(ActividadLaboral::class);
    }
}
