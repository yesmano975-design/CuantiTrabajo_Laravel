# Módulo: Lotes y Terrenos

Gestión de los predios o parcelas de la finca donde se realizan las labores agrícolas.
Define el nombre, referencia y extensión en hectáreas de cada terreno.

---

## Archivos involucrados

| Tipo | Ruta |
|------|------|
| Controlador | `app/Http/Controllers/LoteController.php` |
| Modelo | `app/Models/Lote.php` |
| Vista principal | `resources/views/admin/lotes/index.blade.php` |
| Rutas | `routes/web.php` — group `middleware(['auth', 'no-cache'])` |

---

## Rutas

```
GET    /lotes              → index()    lotes.index
POST   /lotes              → store()    lotes.store
PUT    /lotes/{lote}       → update()   lotes.update
DELETE /lotes/{lote}       → destroy()  lotes.destroy
```

> `create`, `edit` y `show` excluidos del resource. Formularios son modales inline.

---

## Modelo — `Lote`

**Tabla:** `lotes`

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `nombre` | string | Nombre del lote (ej: "Bugangai") |
| `referencia` | string | Código o referencia catastral (ej: "001") |
| `tamano_hectareas` | decimal | Extensión total en hectáreas |
| `descripcion` | string | Notas adicionales (opcional) |

Relaciones:
- `hasMany(ActividadLaboral)` — todas las labores realizadas en este lote

---

## Controlador — `LoteController`

### `index()`
Carga todos los lotes ordenados por nombre.

### `store(Request $request)`
Valida y crea un nuevo lote. La `referencia` debe ser única.

### `update(Request $request, Lote $lote)`
Actualiza los datos del lote. La unicidad de referencia excluye el registro actual.

### `destroy(Lote $lote)`
Elimina el lote. Fallará si tiene actividades laborales asociadas (FK constraint).

---

## Vista — `index.blade.php`

### Estructura
1. **Tabla DataTable** — nombre, referencia, hectáreas, descripción, acciones
2. **Modal `#modalCrear`** — formulario de nuevo lote
3. **Modal `#modalEditar`** — formulario de edición

---

## Campo clave: `tamano_hectareas`

Este campo es la referencia para el **widget de avance de lote** en el módulo de Actividades.
Cuando se registra una nueva actividad, el sistema consulta cuántas hectáreas ya se han
trabajado en ese lote para ese tipo de actividad y muestra el progreso vs el total definido aquí.

---

## Relación con otros módulos

- **Actividades** — cada actividad tiene `lote_id`. El `tamano_hectareas` se usa para calcular el avance y mostrar advertencias al confirmar actividades.
- **Pagos** — el nombre del lote aparece en el desglose de actividades dentro de los recibos de liquidación.
