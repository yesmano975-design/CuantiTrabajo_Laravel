<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: Factura
 * Tabla: facturas
 *
 * Representa el comprobante formal emitido para un pago liquidado.
 * Tiene una relación uno a uno con Pago: cada pago puede tener
 * una factura asociada una vez que fue desembolsado.
 *
 * Columnas:
 *   - pago_id        : FK → pagos (liquidación a la que corresponde)
 *   - fecha_emision  : fecha en que se emitió la factura
 *   - numero_factura : número o código único de la factura
 *
 * Nota: el CRUD de facturas no está implementado en el panel actual.
 * El modelo y la tabla están preparados para una futura funcionalidad
 * de emisión de comprobantes.
 */
class Factura extends Model
{
    protected $table = 'facturas';

    protected $fillable = [
        'pago_id',
        'fecha_emision',
        'numero_factura',
    ];

    // Convierte fecha_emision a instancia Carbon
    protected function casts(): array
    {
        return [
            'fecha_emision' => 'date',
        ];
    }

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Pago (liquidación) al que pertenece esta factura.
     */
    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
}
