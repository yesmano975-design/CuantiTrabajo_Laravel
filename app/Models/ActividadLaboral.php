<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: ActividadLaboral
 * Tabla: actividad_laborals
 *
 * Representa una labor agrícola realizada por un trabajador en un lote
 * en una fecha determinada. Es el registro central del sistema: vincula
 * al trabajador, el lote, la tarifa aplicada y la cantidad ejecutada.
 *
 * Columnas principales:
 *   - valor_actividad_id : FK → valor_actividades (tarifa aplicada)
 *   - lote_id            : FK → lotes (terreno donde se realizó)
 *   - trabajador_id      : FK → trabajadores (quién la ejecutó)
 *   - user_id            : FK → users (quién la registró en el sistema)
 *   - fecha              : fecha en que se realizó la labor
 *   - cantidad           : unidades ejecutadas (ej: 5 hectáreas)
 *   - numero_pasada      : número de veces que se repitió la pasada
 *   - observacion        : notas adicionales opcionales
 *   - estado_confirmacion: 'pendiente' | 'confirmado' | 'rechazado'
 */
class ActividadLaboral extends Model
{
    protected $table = 'actividad_laborals';

    protected $fillable = [
        'valor_actividad_id',
        'lote_id',
        'trabajador_id',
        'user_id',
        'fecha',
        'cantidad',
        'numero_pasada',
        'observacion',
        'estado_confirmacion',
    ];

    // Convierte automáticamente 'fecha' a instancia Carbon al leer
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Tarifa aplicada a esta actividad.
     * A través de ella se obtiene el valor_unitario para calcular el pago.
     */
    public function valorActividad()
    {
        return $this->belongsTo(ValorActividad::class);
    }

    /**
     * Lote/terreno donde se ejecutó la actividad.
     */
    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    /**
     * Trabajador que realizó la labor.
     */
    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }

    /**
     * Usuario del sistema que registró la actividad (auditoría).
     * La FK se llama user_id pero la clase relacionada es User.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Detalles de pago en los que esta actividad fue incluida.
     * Una actividad puede aparecer en un único DetallePago una vez liquidada.
     */
    public function detallePagos()
    {
        return $this->hasMany(DetallePago::class);
    }

    // =========================================================================
    // ACCESSORS (atributos calculados)
    // =========================================================================

    /**
     * getSubtotalAttribute()
     * Calcula el valor económico de la actividad:
     *   subtotal = cantidad × valor_unitario × numero_pasada
     *
     * Se usa en la vista de actividades y en el controlador de pagos
     * para construir el resumen semanal. Devuelve 0 si no hay tarifa asociada.
     */
    public function getSubtotalAttribute(): float
    {
        return $this->cantidad
            * ($this->valorActividad->valor_unitario ?? 0)
            * ($this->numero_pasada ?? 1);
    }
}
