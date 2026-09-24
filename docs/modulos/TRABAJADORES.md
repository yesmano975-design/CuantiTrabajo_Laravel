# Módulo: Trabajadores

Gestión del catálogo de operarios del campo. Permite registrar, editar,
activar/desactivar y eliminar trabajadores vinculados a un cargo.

---

## Archivos involucrados

| Tipo | Ruta |
|------|------|
| Controlador | `app/Http/Controllers/TrabajadorController.php` |
| Modelo | `app/Models/Trabajador.php` |
| Vista principal | `resources/views/admin/trabajadores/index.blade.php` |
| Rutas | `routes/web.php` — group `middleware(['auth', 'no-cache'])` |

---

## Rutas

```
GET    /trabajadores                              → index()       trabajadores.index
POST   /trabajadores                              → store()       trabajadores.store
PUT    /trabajadores/{trabajador}                 → update()      trabajadores.update
DELETE /trabajadores/{trabajador}                 → destroy()     trabajadores.destroy
PATCH  /trabajadores/{trabajador}/toggle-estado  → toggleEstado() trabajadores.toggleEstado
```

> `create`, `edit` y `show` están excluidos del resource. Los formularios son modales inline.

---

## Modelo — `Trabajador`

**Tabla:** `trabajadores`

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `cargo_id` | FK | Cargo del operario (→ `cargos`) |
| `nombre` | string | Nombre del trabajador |
| `apellido` | string | Apellido (opcional) |
| `documento` | string | Documento de identidad (único) |
| `correo` | string | Correo electrónico (opcional) |
| `telefono` | string | Teléfono de contacto (opcional) |
| `estado` | string | `activo` / `inactivo` |

Relaciones:
- `belongsTo(Cargo)` — cargo laboral del trabajador
- `hasMany(ActividadLaboral)` — historial de labores realizadas

---

## Controlador — `TrabajadorController`

### `index()`
Carga todos los trabajadores con `with('cargo')` ordenados por nombre.
Pasa también `$cargos` para el modal de crear/editar.

### `store(Request $request)`
Valida y crea un nuevo trabajador. El `documento` debe ser único.
El `estado` se establece automáticamente como `'activo'`.

### `update(Request $request, Trabajador $trabajador)`
Actualiza los datos. La unicidad del documento excluye el registro actual
para permitir guardar sin cambiar el documento.

### `destroy(Trabajador $trabajador)`
Elimina el trabajador. No debe tener actividades laborales asociadas
(Laravel lanzará error de FK si las tiene).

### `toggleEstado(Trabajador $trabajador)`
Alterna el estado entre `activo` e `inactivo` sin entrar al formulario.
Útil para dar de baja temporalmente a un operario.

---

## Vista — `index.blade.php`

### Estructura
1. **Tabla DataTable** — listado con columnas: nombre, documento, cargo, correo, teléfono, estado, acciones
2. **Modal `#modalCrear`** — formulario de nuevo trabajador
3. **Modal `#modalEditar`** — formulario de edición con datos precargados

### Acciones por fila
- **Editar** — abre `#modalEditar` con los datos del trabajador via `openEditModal()`
- **Toggle estado** — botón que hace PATCH a `toggle-estado` directamente
- **Eliminar** — con confirmación SweetAlert2

---

## Relación con otros módulos

- **Actividades** — cada actividad laboral tiene `trabajador_id`. Solo trabajadores `activos` aparecen en el selector del formulario de actividades.
- **Pagos** — el resumen semanal agrupa por `trabajador_id` para mostrar el desglose individual de cada operario.
- **Cargos** — catálogo auxiliar. Los cargos disponibles se cargan desde la tabla `cargos` (no tiene CRUD propio en el panel actual).
