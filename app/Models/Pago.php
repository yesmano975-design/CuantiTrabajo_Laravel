<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    protected function casts(): array
    {
        return [
            'fecha_generacion' => 'date',
            'periodo_inicio'   => 'date',
            'periodo_fin'      => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function detallePagos()
    {
        return $this->hasMany(DetallePago::class);
    }

    public function factura()
    {
        return $this->hasOne(Factura::class);
    }
}
