<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trabajador extends Model
{
    protected $table = 'trabajadores';

    protected $fillable = [
        'cargo_id',
        'nombre',
        'apellido',
        'documento',
        'correo',
        'telefono',
        'estado',
        'huella',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONES
    |--------------------------------------------------------------------------
    */

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function actividadesLaborales()
    {
        return $this->hasMany(ActividadLaboral::class);
    }
}
