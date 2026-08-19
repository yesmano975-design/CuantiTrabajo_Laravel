<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoActividad extends Model
{
    protected $table = 'tipo_actividades';

    protected $fillable = [
        'nombre',
        'descripcion',
        'unidad_medida',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function valorActividades()
    {
        return $this->hasMany(ValorActividad::class);
    }

    // Tarifas activas vigentes hoy
    public function valorActivo()
    {
        return $this->hasOne(ValorActividad::class)
            ->where('estado', 'activo')
            ->where('fecha_inicio', '<=', now())
            ->where(function ($q) {
                $q->whereNull('fecha_fin')
                  ->orWhere('fecha_fin', '>=', now());
            });
    }
}
