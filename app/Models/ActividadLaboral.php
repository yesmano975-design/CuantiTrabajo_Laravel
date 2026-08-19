<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function valorActividad()
    {
        return $this->belongsTo(ValorActividad::class);
    }

    public function lote()
    {
        return $this->belongsTo(Lote::class);
    }

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detallePagos()
    {
        return $this->hasMany(DetallePago::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    // Calcula el subtotal de la actividad
    public function getSubtotalAttribute(): float
    {
        return $this->cantidad
            * ($this->valorActividad->valor_unitario ?? 0)
            * ($this->numero_pasada ?? 1);
    }
}
