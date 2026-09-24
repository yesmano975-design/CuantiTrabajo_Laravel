# Instancias — Qué son y cómo se usan

---

## ¿Qué es una instancia?

Una **instancia** es un objeto concreto creado a partir de una clase. La clase es el molde (la definición), y la instancia es el objeto real que ya existe en memoria y tiene sus propios datos.

```
Clase       →  Molde / Plantilla / Definición
Instancia   →  Objeto real creado a partir de ese molde
```

### Analogía simple

```
Clase Trabajador    →  define que un trabajador tiene nombre, documento, cargo...
$trabajador         →  instancia concreta de Juan Pérez con su documento y cargo reales
```

---

## Crear una instancia — `new`

La forma más directa es con la palabra clave `new`:

```php
// Crear una instancia de la clase Trabajador
$trabajador = new Trabajador();

// La variable $trabajador ahora ES un objeto de tipo Trabajador
// Puedes asignarle propiedades
$trabajador->nombre    = 'Juan Pérez';
$trabajador->documento = '12345678';
$trabajador->cargo_id  = 2;

// Guardar en la base de datos
$trabajador->save();
```

---

## Obtener una instancia desde la base de datos

Cuando consultas la BD con Eloquent, lo que obtienes **es también una instancia** del modelo:

```php
// Busca el trabajador con id=5 → devuelve UNA instancia de Trabajador
$trabajador = Trabajador::findOrFail(5);

// Ahora puedes leer sus datos
echo $trabajador->nombre;       // 'Juan Pérez'
echo $trabajador->documento;    // '12345678'

// O acceder a sus relaciones
echo $trabajador->cargo->nombre; // 'Recolector'
```

```php
// Busca TODOS → devuelve una COLECCIÓN de instancias
$trabajadores = Trabajador::all();

// Cada elemento de la colección es una instancia individual
foreach ($trabajadores as $trabajador) {
    echo $trabajador->nombre;   // cada $trabajador es su propia instancia
}
```

---

## Diferencia entre clase e instancia

```php
// Esto es llamar un método ESTÁTICO de la clase (sin instancia)
Trabajador::all()           // :: se usa con la clase directamente
Trabajador::findOrFail(5)
Trabajador::create([...])
Trabajador::count()

// Esto es usar un método de INSTANCIA (necesitas el objeto creado)
$trabajador->update([...])  // -> se usa con la instancia
$trabajador->delete()
$trabajador->save()
$trabajador->nombre
$trabajador->cargo->nombre
```

> **Regla fácil:**
> - `::` (doble dos puntos) → sobre la **clase**, sin necesitar objeto creado
> - `->` (flecha) → sobre una **instancia**, el objeto ya existe

---

## Instancias en los controladores del proyecto

En CuantiTrabajo, las instancias aparecen constantemente. Estos son los patrones más usados:

### Patrón 1 — Route Model Binding (el más común)

Laravel inyecta la instancia automáticamente en el método cuando el parámetro de ruta coincide con el tipo del modelo:

```php
// En web.php:
Route::resource('trabajadores', TrabajadorController::class);
// La URL /trabajadores/5/edit tiene el segmento {trabajador}

// En el controlador, Laravel busca el id 5 en la BD
// y te entrega la instancia lista para usar
public function edit(Trabajador $trabajador)
//                   ^^^^^^^^^^^^^^^^^^
//                   Laravel ya buscó el registro con id=5
//                   $trabajador ES la instancia de ese registro
{
    return view('admin.trabajadores.edit', compact('trabajador'));
}

public function update(Request $request, Trabajador $trabajador)
{
    $trabajador->update($request->validated()); // actualiza ESA instancia
    return redirect()->route('trabajadores.index');
}

public function destroy(Trabajador $trabajador)
{
    $trabajador->delete(); // elimina ESA instancia
    return redirect()->route('trabajadores.index');
}
```

### Patrón 2 — Buscar manualmente con `findOrFail`

```php
public function edit($id)
{
    $trabajador = Trabajador::findOrFail($id);
    // $trabajador ahora es una instancia del trabajador con ese id
    // Si no existe, Laravel devuelve automáticamente un error 404

    return view('admin.trabajadores.edit', compact('trabajador'));
}
```

### Patrón 3 — Crear una instancia nueva con `create()`

```php
public function store(Request $request)
{
    $validated = $request->validate([...]);

    // create() crea la instancia Y la guarda en la BD en un solo paso
    $trabajador = Trabajador::create($validated);
    // $trabajador ahora es la instancia recién creada, con su id asignado

    return redirect()->route('trabajadores.index');
}
```

### Patrón 4 — Instancia con relaciones (PagoController)

```php
public function show(Pago $pago)
{
    // $pago es la instancia del pago
    // with() carga las instancias relacionadas de una sola vez
    $pago->load('trabajador', 'detalles.valorActividad.tipoActividad');

    // Ahora puedes navegar por las instancias relacionadas
    echo $pago->trabajador->nombre;                          // instancia Trabajador
    echo $pago->detalles->first()->valorActividad->precio;  // instancia ValorActividad
}
```

---

## Instancias en las vistas Blade

Cuando pasas una instancia con `compact()` a la vista, Blade puede usarla directamente:

```php
// En el controlador
$trabajador = Trabajador::findOrFail($id);
return view('admin.trabajadores.edit', compact('trabajador'));
```

```blade
{{-- En la vista, $trabajador es la instancia que llegó desde el controlador --}}

{{-- Leer datos de la instancia --}}
<input type="text" value="{{ $trabajador->nombre }}">
<input type="text" value="{{ $trabajador->documento }}">

{{-- Acceder a la relación (instancia del Cargo relacionado) --}}
<p>Cargo actual: {{ $trabajador->cargo->nombre }}</p>

{{-- Usar el id de la instancia para construir la ruta --}}
<form action="{{ route('trabajadores.update', $trabajador->id) }}" method="POST">
```

---

## Instancias vs colecciones

| | Instancia | Colección |
|---|---|---|
| **Qué es** | Un solo objeto / registro | Varios objetos juntos |
| **Cómo se obtiene** | `findOrFail($id)`, `find($id)`, `new Clase()` | `all()`, `get()`, `where()->get()` |
| **Ejemplo** | `$trabajador` (Juan Pérez) | `$trabajadores` (todos los trabajadores) |
| **Para acceder** | `$trabajador->nombre` | `foreach($trabajadores as $t)` |
| **Tiene métodos de colección** | No | Sí: `->count()`, `->first()`, `->pluck()` |

```php
// Instancia — un solo registro
$lote = Lote::findOrFail(3);
echo $lote->nombre;   // 'Lote Norte'

// Colección — varios registros
$lotes = Lote::all();
echo $lotes->count(); // 8
foreach ($lotes as $lote) { ... }
```

---

## Errores comunes con instancias

### Error 1 — Llamar método de instancia sobre la clase

```php
// MAL — delete() es método de instancia, no de clase
Trabajador::delete();   // Error: Non-static method called statically

// BIEN — primero obtienes la instancia, luego llamas delete()
$trabajador = Trabajador::findOrFail($id);
$trabajador->delete();
```

### Error 2 — Usar `->` sobre null

```php
// Si el trabajador no tiene cargo asignado (cargo_id = null)
$trabajador->cargo->nombre  // Error: Attempt to read property on null
//               ↑ cargo es null, no hay instancia que tenga ->nombre

// BIEN — verificar antes de acceder
$trabajador->cargo?->nombre         // null safe operator (PHP 8+)
$trabajador->cargo->nombre ?? '—'  // fallback si es null
```

### Error 3 — Confundir cuándo guardar

```php
// new + asignar propiedades → necesitas save() al final
$trabajador = new Trabajador();
$trabajador->nombre = 'Ana';
$trabajador->save();   // ← sin esto, NO se guarda en la BD

// create() → guarda automáticamente, no necesita save()
$trabajador = Trabajador::create(['nombre' => 'Ana']);
```

---

## Resumen rápido

```
new Clase()               → crea instancia vacía en memoria (sin guardar)
Clase::create([...])      → crea instancia y la guarda en BD de una vez
Clase::findOrFail($id)    → obtiene instancia existente de la BD
Clase::all() / get()      → obtiene colección de instancias

$instancia->propiedad     → lee un dato de la instancia
$instancia->relacion      → accede a otra instancia relacionada
$instancia->update([...]) → actualiza la instancia en la BD
$instancia->delete()      → elimina la instancia de la BD
$instancia->save()        → guarda los cambios hechos con ->propiedad = valor
```

---

---

## Códigos de error HTTP — Qué significan y dónde buscar

Cuando algo falla en el navegador, el código de error te dice en qué capa está el problema.

---

### Errores 4xx — El problema está en la petición o los datos

| Código | Nombre | Qué pasó | Dónde buscar |
|---|---|---|---|
| **400** | Bad Request | La petición tiene datos mal formados | Validación en el controlador (`$request->validate`) |
| **401** | Unauthorized | No estás autenticado (no has iniciado sesión) | Middleware `auth`, rutas protegidas en `web.php` |
| **403** | Forbidden | Estás autenticado pero no tienes permiso | Middleware `CheckRol`, lógica de autorización |
| **404** | Not Found | La ruta o el recurso no existe | `routes/web.php`, nombre de la ruta, `findOrFail($id)` |
| **405** | Method Not Allowed | Usaste GET donde se espera POST (o viceversa) | El método HTTP en el `<form>` o en la ruta (`Route::post`) |
| **419** | Page Expired | Falta el token CSRF en el formulario | Falta `@csrf` dentro del `<form>` en la vista Blade |
| **422** | Unprocessable Entity | Validación falló (datos incorrectos) | Reglas en `$request->validate([...])` del controlador |
| **429** | Too Many Requests | Demasiadas peticiones en poco tiempo | Middleware de rate limiting |

---

### Errores 5xx — El problema está en el servidor (tu código PHP)

| Código | Nombre | Qué pasó | Dónde buscar |
|---|---|---|---|
| **500** | Internal Server Error | Error genérico de PHP / Laravel | Revisa `storage/logs/laravel.log`, activa `APP_DEBUG=true` en `.env` |
| **502** | Bad Gateway | El servidor web no puede conectar con PHP | Problema de configuración de Laragon / Apache |
| **503** | Service Unavailable | App en modo mantenimiento o caída | `php artisan down` fue ejecutado, usa `php artisan up` |
| **504** | Gateway Timeout | La petición tardó demasiado | Consulta muy lenta a la BD, bucle infinito en el código |

> **El más común en desarrollo es el 500.** Siempre revisa `storage/logs/laravel.log` para ver el error real con archivo y línea exacta.

---

### Errores de Laravel específicos (no son códigos HTTP pero los verás seguido)

| Mensaje | Qué pasó | Dónde buscar |
|---|---|---|
| `View [x] not found` | La vista no existe con ese nombre | `resources/views/` — revisa el nombre y la carpeta |
| `Route [x] not defined` | La ruta con ese nombre no existe | `routes/web.php` — revisa el `->name('...')` |
| `Class x not found` | El controlador no existe o tiene mal namespace | `app/Http/Controllers/` — nombre del archivo y clase |
| `Method x not found` | El método del controlador fue renombrado o borrado | El controlador correspondiente |
| `Column not found` | Un campo no existe en la tabla | Migración, `$fillable` del modelo, nombre del campo en la vista |
| `Attempt to read property on null` | Accediste a `->propiedad` sobre un `null` | El controlador donde se pasa la variable a la vista |
| `SQLSTATE` | Error de base de datos | Consulta Eloquent, nombre de tabla/columna en el modelo |

---

### Flujo para diagnosticar cualquier error

```
1. ¿Qué código o mensaje aparece en pantalla?
        ↓
2. ¿Es 4xx o 5xx?
   4xx → problema en rutas, permisos, formularios, validación
   5xx → problema en código PHP, revisa el log
        ↓
3. Abrir storage/logs/laravel.log → busca la última línea [ERROR]
   Te dice: archivo exacto + número de línea
        ↓
4. Ir a ese archivo y línea a corregir
        ↓
5. Si no encuentras nada: php artisan optimize:clear
```

---

*Documento generado para repaso — Proyecto CuantiTrabajo Laravel*
