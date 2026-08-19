<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Pago
 * Tabla: pagos
 *
 * Representa la cabecera de una liquidación semanal de pagos.
 * Agrupa todos los DetallePago del periodo lunes–sábado seleccionado
 * y almacena el total consolidado. El ciclo de vida es:
 *   pendiente → pagado
 *
 * Columnas:
 *   - fecha_generacion : fecha en que se generó la liquidación
 *   - periodo_inicio   : lunes de la semana liquidada
 *   - periodo_fin      : sábado de la semana liquidada
 *   - total_pago       : suma de todos los subtotales del periodo
 *   - estado           : 'pendiente' | 'pagado'
 */
class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'fecha_generacion',
        'periodo_inicio',
        'periodo_fin',
        'total_pago',
        'estado',
    ];

    // Convierte automáticamente las fechas a instancias Carbon
    protected function casts(): array
    {
        return [
            'fecha_generacion' => 'date',
            'periodo_inicio'   => 'date',
            'periodo_fin'      => 'date',
        ];
    }

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Renglones de detalle que componen esta liquidación.
     * Cada detalle representa una actividad laboral incluida en el pago.
     */
    public function detallePagos()
    {
        return $this->hasMany(DetallePago::class);
    }

    /**
     * Factura asociada a este pago (relación uno a uno).
     * Permite emitir un comprobante formal una vez que el pago está generado.
     */
    public function factura()
    {
        return $this->hasOne(Factura::class);
    }
}
