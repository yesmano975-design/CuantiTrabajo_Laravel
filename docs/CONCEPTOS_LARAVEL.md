L# Conceptos Laravel — Guía de Repaso CuantiTrabajo

---

## 1. Verbos HTTP

Son el "tipo de acción" que hace una petición al servidor. El navegador los usa para indicar qué quiere hacer.

| Verbo | ¿Qué significa? | Ejemplo en el proyecto |
|---|---|---|
| `GET` | Pedir / ver información | Ver la lista de trabajadores |
| `POST` | Enviar datos nuevos | Guardar un trabajador nuevo |
| `PUT / PATCH` | Actualizar datos existentes | Editar un lote |
| `DELETE` | Eliminar un registro | Borrar una actividad |

> **Nota:** Los formularios HTML solo soportan GET y POST. Para PUT, PATCH y DELETE, Laravel usa un campo oculto `@method('PUT')` dentro del formulario, que le indica al servidor qué verbo usar realmente.

---

## 2. Métodos del Resource Controller

Cuando defines `Route::resource('trabajadores', TrabajadorController::class)`, Laravel registra automáticamente **7 rutas** y espera que el controlador tenga estos métodos:

| Método | Verbo HTTP | URL generada | ¿Qué hace? |
|---|---|---|---|
| `index()` | GET | `/trabajadores` | Lista todos los registros |
| `create()` | GET | `/trabajadores/create` | Muestra el formulario vacío para crear |
| `store()` | POST | `/trabajadores` | Recibe y guarda el formulario de creación |
| `show()` | GET | `/trabajadores/{id}` | Muestra el detalle de un registro |
| `edit()` | GET | `/trabajadores/{id}/edit` | Muestra el formulario con datos para editar |
| `update()` | PUT/PATCH | `/trabajadores/{id}` | Recibe y guarda los cambios del formulario de edición |
| `destroy()` | DELETE | `/trabajadores/{id}` | Elimina el registro |

### Flujo crear:
```
Usuario llena formulario → POST /trabajadores → store() → guarda en BD → redirect
```

### Flujo editar:
```
Usuario abre formulario → GET /trabajadores/5/edit → edit() → muestra datos
Usuario guarda cambios  → PUT /trabajadores/5      → update() → actualiza en BD → redirect
```

---

## 3. Request — ¿Qué es y para qué sirve?

`Request` es el objeto que contiene **todo lo que el usuario envió** al servidor: campos del formulario, archivos, parámetros de URL, cookies, etc.

```php
// Se inyecta como parámetro en el método
public function store(Request $request)
{
    // Acceder a un campo del formulario
    $request->nombre       // valor del input name="nombre"
    $request->email        // valor del input name="email"

    // Acceder a todos los campos a la vez
    $request->all()        // array con todos los campos

    // Solo algunos campos
    $request->only('nombre', 'email')

    // Todos menos algunos
    $request->except('_token', '_method')

    // Verificar si existe un campo
    $request->has('telefono')

    // Valor con fallback si no existe
    $request->get('telefono', 'sin teléfono')

    // Para checkboxes (devuelve true/false)
    $request->boolean('activo')
}
```

---

## 4. Validación — `$request->validate()`

Antes de guardar en la base de datos, siempre se validan los datos. Si la validación falla, Laravel redirige automáticamente al formulario con los errores.

```php
$request->validate([
    'nombre'    => 'required|string|max:100',
    'documento' => 'required|string|unique:trabajadores,documento',
    'correo'    => 'nullable|email|max:150',
    'cargo_id'  => 'required|exists:cargos,id',
]);
```

### Reglas más comunes:

| Regla | ¿Qué valida? |
|---|---|
| `required` | El campo no puede estar vacío |
| `nullable` | El campo puede estar vacío o nulo |
| `string` | Debe ser texto |
| `numeric` | Debe ser número |
| `email` | Debe tener formato de correo |
| `max:100` | Máximo 100 caracteres |
| `min:0.01` | Valor mínimo |
| `unique:tabla,columna` | No puede repetirse en la BD |
| `exists:tabla,columna` | Debe existir en la BD (para IDs foráneos) |

> **Truco:** `unique` con excepción para editar:
> ```php
> 'documento' => 'unique:trabajadores,documento,' . $trabajador->id
> // Ignora el propio registro al verificar unicidad
> ```

---

## 5. Eloquent — Operaciones básicas en la BD

Eloquent es el ORM de Laravel. Permite interactuar con la base de datos usando objetos PHP en lugar de SQL directo.

```php
// Obtener todos los registros
Trabajador::all()

// Obtener todos ordenados
Trabajador::orderBy('nombre')->get()

// Buscar por ID (lanza error 404 si no existe)
Trabajador::findOrFail($id)

// Crear un registro nuevo
Trabajador::create([
    'nombre'   => 'Juan',
    'cargo_id' => 1,
]);

// Actualizar un registro existente
$trabajador->update([
    'nombre' => 'Juan Editado',
]);

// Eliminar un registro
$trabajador->delete()

// Contar registros
Trabajador::count()
$trabajador->actividadesLaborales()->count()
```

---

## 6. Relaciones Eloquent

Permiten acceder a datos relacionados de otras tablas sin escribir SQL con JOINs.

```php
// En el modelo Trabajador
public function cargo() {
    return $this->belongsTo(Cargo::class);  // trabajador pertenece a un cargo
}

public function actividadesLaborales() {
    return $this->hasMany(ActividadLaboral::class);  // trabajador tiene muchas actividades
}

// Uso en el controlador
$trabajador->cargo->nombre      // accede al nombre del cargo relacionado
$trabajador->actividadesLaborales()->count()  // cuenta sus actividades
```

### Eager Loading — `with()`

Carga las relaciones de una sola consulta para evitar el problema N+1:

```php
// Sin with() → 1 consulta por cada trabajador (lento)
Trabajador::all()

// Con with() → solo 2 consultas en total (rápido)
Trabajador::with('cargo')->get()
```

---

## 7. compact() y la vista

`compact()` empaqueta variables PHP para enviarlas a la vista Blade. El nombre en el string **debe coincidir exactamente** con el nombre de la variable.

```php
$lotes          = Lote::all();       // variable: $lotes
$totalHectareas = 150;               // variable: $totalHectareas

return view('admin.lotes.index', compact('lotes', 'totalHectareas'));
//                                          ↑             ↑
//                               mismo nombre que la variable PHP
```

En la vista Blade se usan directamente:
```blade
@foreach($lotes as $lote)
    {{ $lote->nombre }}
@endforeach

Total: {{ $totalHectareas }} ha
```

> **Error clásico:** `$Lotes` (mayúscula) + `compact('lotes')` (minúscula) → `Undefined variable $lotes`

---

## 8. Redirect y mensajes flash

Después de guardar, editar o eliminar, siempre se redirige al usuario y se le muestra un mensaje de confirmación.

```php
// Redirigir a una ruta con nombre
return redirect()->route('trabajadores.index')
    ->with('success', 'Trabajador guardado correctamente.');

// Redirigir con mensaje de error
return redirect()->route('trabajadores.index')
    ->with('error', 'No se puede eliminar.');

// Redirigir de vuelta al formulario (cuando falla validación)
return back()->withErrors(['email' => 'El correo ya existe']);
```

En la vista se muestra el mensaje:
```blade
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
```

---

## 9. use — Importar clases

Al inicio de cada archivo PHP se declaran las clases que se van a usar con `use`. Sin esto, PHP no sabe dónde encontrar la clase.

```php
// Correcto
use App\Models\Trabajador;
use App\Models\Cargo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Si falta un use, por ejemplo:
// use App\Models\Cargo;  ← eliminado

Cargo::all()  // → Error: Class "App\Http\Controllers\Cargo" not found
//               PHP busca Cargo en el namespace actual del archivo
```

---

## 10. Transacciones de BD — DB::beginTransaction()

Se usan cuando una operación requiere **múltiples inserciones/eliminaciones** y necesitas que o todas pasen, o ninguna.

```php
DB::beginTransaction();
try {
    $pago = Pago::create([...]);          // inserción 1
    DetallePago::create([...]);           // inserción 2, 3, 4...

    DB::commit();   // si todo salió bien → confirma los cambios
} catch (\Exception $e) {
    DB::rollBack(); // si algo falló → revierte todo, como si nada hubiera pasado
    return redirect()->back()->with('error', $e->getMessage());
}
```

> En el proyecto se usa en `PagoController@store` y `PagoController@destroy` para que un pago nunca quede a medias generado.

---

## 11. Middleware

Código que se ejecuta **antes** de que llegue la petición al controlador. Actúa como un filtro.

```php
// En web.php
Route::middleware(['auth', 'role:administrador', 'no-cache'])->group(function () {
    // estas rutas solo las accede un administrador autenticado
});
```

| Middleware | ¿Qué hace? |
|---|---|
| `auth` | Verifica que el usuario esté logueado, si no lo redirige al login |
| `role:administrador` | Verifica que el usuario tenga ese rol, si no devuelve 403 |
| `no-cache` | Agrega headers HTTP para que el navegador no guarde las páginas en caché |

---

## 12. Route::resource vs rutas manuales

```php
// Una sola línea genera las 7 rutas del CRUD completo
Route::resource('lotes', LoteController::class);

// Equivale a escribir manualmente:
Route::get   ('lotes',            [LoteController::class, 'index']);
Route::get   ('lotes/create',     [LoteController::class, 'create']);
Route::post  ('lotes',            [LoteController::class, 'store']);
Route::get   ('lotes/{lote}',     [LoteController::class, 'show']);
Route::get   ('lotes/{lote}/edit',[LoteController::class, 'edit']);
Route::put   ('lotes/{lote}',     [LoteController::class, 'update']);
Route::delete('lotes/{lote}',     [LoteController::class, 'destroy']);

// Ruta extra fuera del resource (no es parte del CRUD estándar)
Route::patch('lotes/{lote}/toggle-estado', [LoteController::class, 'toggleEstado'])
    ->name('lotes.toggleEstado');
```

---

## 13. .env — Variables de entorno

Archivo de configuración local que **no se sube a Git**. Contiene datos sensibles del entorno.

```env
APP_NAME=CuantiTrabajo      # nombre de la app
APP_ENV=local               # entorno: local, production
APP_DEBUG=true              # mostrar errores detallados (solo en local)
APP_URL=http://localhost    # URL base de la app

DB_CONNECTION=mysql         # motor de base de datos
DB_HOST=127.0.0.1           # servidor de BD
DB_PORT=3306                # puerto (Laragon usa 3306 o 3320)
DB_DATABASE=cuantitrabajo_laravel  # nombre exacto de la BD
DB_USERNAME=root            # usuario
DB_PASSWORD=                # contraseña
```

> **Error clásico:** `DB_DATABASE` con nombre incorrecto → `SQLSTATE[HY000] [1049] Unknown database`
> **Importante:** después de cambiar el `.env` correr `php artisan config:clear`

---

*Documento generado para repaso — Proyecto CuantiTrabajo Laravel*
