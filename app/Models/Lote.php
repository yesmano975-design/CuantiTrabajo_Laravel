<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Lote
 * Tabla: lotes
 *
 * Representa una parcela o terreno de la finca donde se realizan las labores.
 * Cada actividad laboral debe estar asociada a un lote para registrar
 * dónde se ejecutó el trabajo.
 *
 * Columnas:
 *   - nombre           : nombre descriptivo del lote (ej: "Lote Norte")
 *   - referencia       : código único de identificación de campo
 *   - ubicacion        : descripción textual de la ubicación (opcional)
 *   - tamano_hectareas : superficie del terreno en hectáreas
 */
class Lote extends Model
{
    protected $table = 'lotes';

    protected $fillable = [
        'nombre',
        'ubicacion',
        'referencia',
        'tamano_hectareas',
    ];

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Actividades laborales realizadas en este lote.
     * Se usa para verificar si el lote puede eliminarse
     * (no se puede si tiene actividades asociadas).
     */
    public function actividadesLaborales()
    {
        return $this->hasMany(ActividadLaboral::class);
    }
}
