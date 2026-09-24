# Módulo: Pagos y Liquidaciones

Gestión de la nómina semanal. Agrupa las actividades confirmadas por semana,
genera la liquidación y registra el historial de pagos desembolsados.

---

## Archivos involucrados

| Tipo | Ruta |
|------|------|
| Controlador | `app/Http/Controllers/PagoController.php` |
| Modelos | `app/Models/Pago.php`, `app/Models/DetallePago.php` |
| Vista principal | `resources/views/admin/pagos/index.blade.php` |
| Vista recibo | `resources/views/admin/pagos/show.blade.php` |
| Rutas | `routes/web.php` — group `middleware(['auth', 'no-cache'])` |

---

## Rutas

```
GET    /pagos                                → index()              pagos.index
POST   /pagos                                → store()              pagos.store
GET    /pagos/{pago}                         → show()               pagos.show
DELETE /pagos/{pago}                         → destroy()            pagos.destroy
PATCH  /pagos/{pago}/marcar-pagado          → marcarPagado()       pagos.marcarPagado
PATCH  /pagos/{pago}/agregar-actividades    → agregarActividades() pagos.agregarActividades
```

---

## Modelos

### `Pago` — cabecera de liquidación
**Tabla:** `pagos`

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `fecha_generacion` | date | Fecha en que se generó |
| `periodo_inicio` | date | Lunes de la semana liquidada |
| `periodo_fin` | date | Sábado de la semana liquidada |
| `total_pago` | decimal | Suma de todos los subtotales |
| `estado` | string | `pendiente` / `pagado` |

Relaciones: `hasMany(DetallePago)`, `hasOne(Factura)`

### `DetallePago` — renglón de liquidación
**Tabla:** `detalle_pagos`

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `pago_id` | FK | Liquidación a la que pertenece |
| `actividad_laboral_id` | FK | Actividad incluida |
| `cantidad` | integer | Snapshot al momento de liquidar |
| `valor_unitario` | decimal | Snapshot de la tarifa al liquidar |
| `subtotal` | decimal | cantidad × valor_unitario × pasadas |

> Los valores son snapshots históricos — cambios futuros en tarifas no afectan liquidaciones pasadas.

---

## Controlador — `PagoController`

### `index()`
Hace dos consultas principales:

1. **`$semanas`** — agrupa actividades `confirmadas` por semana (lunes–sábado) usando SQL con `DATE_SUB` y `DAYOFWEEK`. Calcula `num_trabajadores`, `num_actividades` y `total_semana` por semana.

2. **`$historial`** — todos los pagos generados con eager loading completo de relaciones para los modales inline.

Luego determina la semana seleccionada (por GET o la primera disponible) y construye `$resumenSemana`:
- Si la semana **ya fue liquidada**: carga los datos del `Pago` existente + calcula actividades pendientes de incluir (`$actividadesPendientes`)
- Si la semana **no fue liquidada**: calcula desde actividades confirmadas

Variables enviadas a la vista: `semanas`, `historial`, `lunesActual`, `sabadoActual`, `resumenSemana`, `totalSemana`, `yaGenerado`, `pagoExistente`, `actividadesPendientes`

### `store(Request $request)`
Genera la liquidación semanal dentro de una transacción DB:
1. Valida que no exista pago duplicado para ese período
2. Obtiene todas las actividades confirmadas del rango
3. Crea el `Pago` (cabecera) con `estado = 'pendiente'`
4. Crea un `DetallePago` por cada actividad con snapshot de valores
5. `DB::commit()` o `DB::rollBack()` si falla

### `agregarActividades(Request $request, Pago $pago)`
Agrega actividades nuevas a un pago existente (solo si está `pendiente`). Filtra duplicados, crea los `DetallePago` faltantes e incrementa el `total_pago` del encabezado.

### `marcarPagado(Pago $pago)`
Cambia `estado` de `pendiente` a `pagado`. Irreversible.

### `destroy(Pago $pago)`
Elimina un pago y todos sus detalles. Solo permitido en estado `pendiente`.

### `show(Pago $pago)`
Carga el recibo completo con eager loading anidado y agrupa por trabajador para la vista de impresión.

---

## Vista — `index.blade.php`

### Layout: 2 columnas

**Columna izquierda (4/12):**
- Lista de semanas disponibles con badge de estado (Sin liquidar / Generado / Pagado) y total
- Cada semana es un enlace GET que recarga la página con `?lunes=X&sabado=Y`

**Columna derecha (8/12):**
- Header de semana + badge de estado de liquidación
- Lista de operarios de la semana (clickeables) con nombre, cargo, labores y total
- Badge ámbar con cantidad de actividades pendientes si las hay

### Modales por trabajador
Al hacer clic en un operario de la lista se abre `#modalTrabajador{idx}` con:
- Período y total del trabajador
- Tabla de actividades (fecha, labor, lote, cant., pasadas, v. unit., subtotal)
- Si hay actividades pendientes: formulario con checkboxes para agregarlas al pago
- Botón "Generar Liquidación Semana" si la semana no está liquidada

### Modales del historial
Al hacer clic en el ojo de una fila del historial se abre `#modalPago{id}` con el recibo completo inline.

### Historial (DataTable)
Tabla con todos los pagos generados: ID, fecha, período, cantidad de ítems, total, estado (Pagado/Generado) y acciones (ver, marcar pagado, eliminar).

---

## Flujo principal

```
Admin selecciona semana en lista izquierda
    ↓
Ve lista de operarios con sus totales
    ↓
Clic en operario → modal con detalle
    ↓
Botón "Generar Liquidación Semana" → POST /pagos
    ↓
Se crean Pago + DetallePagos (transacción DB)
    ↓
Semana queda como "Generado" (pendiente de desembolso)
    ↓
Admin marca como pagado → estado: pagado
```

---

## Relación con otros módulos

- **Actividades** — solo las actividades con `estado_confirmacion = 'confirmado'` son liquidables
- **Trabajadores** — el resumen agrupa por `trabajador_id` para mostrar el desglose individual
- **Tarifas** — el `valor_unitario` se copia al `DetallePago` como snapshot histórico
