<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Cargo
 * Tabla: cargos
 *
 * Representa el puesto o función que desempeña un trabajador en la finca
 * (ej: Tractorista, Fumigador, Abonador, Regador).
 * Los cargos son un catálogo estático que se gestiona directamente en BD;
 * no tienen CRUD en el panel actualmente.
 *
 * Columnas:
 *   - nombre     : nombre del cargo
 *   - descripcion: descripción opcional del rol en la finca
 */
class Cargo extends Model
{
    protected $table = 'cargos';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Trabajadores que tienen este cargo asignado.
     */
    public function trabajadores()
    {
        return $this->hasMany(Trabajador::class);
    }
}
