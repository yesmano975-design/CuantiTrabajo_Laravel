<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo: TipoActividad
 * Tabla: tipo_actividades
 *
 * Define el catálogo de labores agrícolas (ej: Fumigación, Poda, Abonado).
 * Cada tipo tiene una unidad de medida que indica cómo se cuantifica
 * la ejecución de esa labor: por horas, días o hectáreas.
 * A un tipo se le pueden asignar múltiples tarifas con distintas vigencias.
 *
 * Columnas:
 *   - nombre       : nombre de la actividad (ej: "Fumigación")
 *   - descripcion  : detalles o especificaciones técnicas (opcional)
 *   - unidad_medida: 'horas' | 'dias' | 'hectareas'
 */
class TipoActividad extends Model
{
    protected $table = 'tipo_actividades';

    protected $fillable = [
        'nombre',
        'descripcion',
        'unidad_medida',
    ];

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Todas las tarifas (históricas y vigentes) asociadas a este tipo.
     */
    public function valorActividades()
    {
        return $this->hasMany(ValorActividad::class);
    }

    /**
     * valorActivo()
     * Relación que retorna solo la tarifa activa y vigente hoy.
     * Útil para mostrar el precio actual sin traer todo el historial.
     * Filtra: estado='activo', fecha_inicio <= hoy, fecha_fin >= hoy (o sin fin).
     */
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
