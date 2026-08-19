<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: ValorActividad
 * Tabla: valor_actividades
 *
 * Representa la tarifa económica de un tipo de actividad para un rango
 * de fechas determinado. Permite tener historial de precios:
 * si el valor por hectárea de fumigación cambia, se puede crear
 * una nueva tarifa con nueva fecha_inicio sin perder el historial anterior.
 *
 * Columnas:
 *   - tipo_actividad_id : FK → tipo_actividades (labor que se tarifea)
 *   - valor_unitario    : precio por unidad de medida (en moneda local)
 *   - fecha_inicio      : desde cuándo aplica esta tarifa
 *   - fecha_fin         : hasta cuándo aplica (NULL = sin vencimiento)
 *   - estado            : 'activo' | 'inactivo'
 *
 * Solo las tarifas activas y vigentes hoy se muestran al registrar actividades.
 */
class ValorActividad extends Model
{
    protected $table = 'valor_actividades';

    protected $fillable = [
        'tipo_actividad_id',
        'valor_unitario',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    // Convierte las fechas a instancias Carbon automáticamente
    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin'    => 'date',
        ];
    }

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Tipo de actividad al que pertenece esta tarifa.
     */
    public function tipoActividad()
    {
        return $this->belongsTo(TipoActividad::class);
    }

    /**
     * Actividades laborales que usaron esta tarifa.
     * Se usa para bloquear la eliminación de tarifas con historial.
     */
    public function actividadesLaborales()
    {
        return $this->hasMany(ActividadLaboral::class);
    }
}
