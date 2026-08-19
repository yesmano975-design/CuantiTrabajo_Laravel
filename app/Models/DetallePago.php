<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: DetallePago
 * Tabla: detalle_pagos
 *
 * Representa el renglón de una liquidación de pago.
 * Cada DetallePago vincula un Pago con una ActividadLaboral específica,
 * y almacena una "fotografía" de los valores en el momento de generar
 * el pago (cantidad, valor_unitario, subtotal), garantizando que los
 * datos históricos no cambien aunque se modifiquen las tarifas después.
 *
 * Columnas:
 *   - pago_id              : FK → pagos (liquidación a la que pertenece)
 *   - actividad_laboral_id : FK → actividad_laborals (actividad incluida)
 *   - cantidad             : copia de la cantidad al momento de liquidar
 *   - valor_unitario       : copia del valor de la tarifa al momento de liquidar
 *   - subtotal             : resultado de cantidad × valor_unitario × numero_pasada
 */
class DetallePago extends Model
{
    protected $table = 'detalle_pagos';

    protected $fillable = [
        'pago_id',
        'actividad_laboral_id',
        'cantidad',
        'valor_unitario',
        'subtotal',
    ];

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Liquidación (cabecera) a la que pertenece este detalle.
     */
    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }

    /**
     * Actividad laboral que origina este renglón de pago.
     */
    public function actividadLaboral()
    {
        return $this->belongsTo(ActividadLaboral::class);
    }
}
