<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modelo: User
 * Tabla: users
 *
 * Representa a los usuarios que acceden al panel de administración.
 * Extiende Authenticatable para integrarse con el sistema de autenticación
 * de Laravel (sesiones, middleware auth, etc.).
 *
 * Columnas:
 *   - rol_id   : FK → roles (determina los permisos del usuario)
 *   - nombre   : nombre del usuario
 *   - apellido : apellido (opcional)
 *   - email    : correo electrónico, usado como credencial de inicio de sesión
 *   - password : contraseña hasheada en bcrypt (nunca en texto plano)
 *   - telefono : teléfono de contacto (opcional)
 *   - estado   : 'activo' | 'inactivo'
 *
 * Campos ocultos (no se serializan en JSON/arrays):
 *   - password, remember_token
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'rol_id',
        'nombre',
        'apellido',
        'email',
        'password',
        'telefono',
        'estado',
    ];

    // Estos campos nunca se incluyen en respuestas JSON ni arrays
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // El cast 'hashed' aplica automáticamente bcrypt al asignar la contraseña
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // =========================================================================
    // RELACIONES
    // =========================================================================

    /**
     * Rol asignado al usuario (administrador o secretaria).
     * El middleware CheckRol lee rol->nombre para controlar el acceso.
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    /**
     * Actividades laborales registradas por este usuario en el sistema.
     * Permite auditar quién digitó cada registro.
     */
    public function actividadesLaborales()
    {
        return $this->hasMany(ActividadLaboral::class);
    }
}
