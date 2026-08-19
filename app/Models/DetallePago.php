<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }

    public function actividadLaboral()
    {
        return $this->belongsTo(ActividadLaboral::class);
    }
}
