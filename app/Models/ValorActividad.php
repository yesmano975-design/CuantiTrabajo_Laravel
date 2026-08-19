<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin'    => 'date',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function tipoActividad()
    {
        return $this->belongsTo(TipoActividad::class);
    }

    public function actividadesLaborales()
    {
        return $this->hasMany(ActividadLaboral::class);
    }
}
