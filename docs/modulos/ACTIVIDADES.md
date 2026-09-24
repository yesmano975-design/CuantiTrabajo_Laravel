# Módulo: Actividades Laborales

Registro diario de las labores agrícolas realizadas por cada operario en un lote.
Es el núcleo del sistema: cada actividad origina el cálculo de subtotales y alimenta la liquidación semanal.

---

## Archivos involucrados

| Tipo | Ruta |
|------|------|
| Controlador | `app/Http/Controllers/ActividadLaboralController.php` |
| Modelo | `app/Models/ActividadLaboral.php` |
| Vista principal | `resources/views/admin/actividades/index.blade.php` |
| Rutas | `routes/web.php` — group `middleware(['auth', 'no-cache'])` |

---

## Rutas

```
GET    /actividades                          → index()        actividades.index
POST   /actividades                          → store()        actividades.store
GET    /actividades/{actividad}              → show()         actividades.show
PUT    /actividades/{actividad}              → update()       actividades.update
DELETE /actividades/{actividad}              → destroy()      actividades.destroy
PATCH  /actividades/{actividad}/confirmar   → confirmar()    actividades.confirmar
GET    /actividades/avance-lote             → avanceLote()   actividades.avanceLote
```

> `create` y `edit` están excluidos del resource (`->except(['create', 'edit'])`).
> Los formularios son modales inline en `index.blade.php`.

---

## Modelo — `ActividadLaboral`

**Tabla:** `actividad_laborals`

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `valor_actividad_id` | FK | Tarifa aplicada (→ `valor_actividades`) |
| `lote_id` | FK | Lote donde se realizó la labor |
| `trabajador_id` | FK | Operario que ejecutó la actividad |
| `user_id` | FK | Usuario del sistema que registró el dato |
| `fecha` | date | Fecha de la labor (no puede ser futura) |
| `cantidad` | integer | Unidades ejecutadas (ha, bultos, etc.) |
| `numero_pasada` | integer | Número de pasadas realizadas |
| `observacion` | string | Notas opcionales |
| `estado_confirmacion` | string | `pendiente` / `confirmado` / `rechazado` |

**Accessor calculado:**
```php
getSubtotalAttribute() → cantidad × valor_unitario × numero_pasada
```

**Relaciones:**
- `belongsTo(ValorActividad)` — tarifa con precio por unidad
- `belongsTo(Lote)` — terreno donde se trabajó
- `belongsTo(Trabajador)` — operario
- `belongsTo(User, 'user_id')` — auditoría de quién registró
- `hasMany(DetallePago)` — aparece en liquidaciones

---

## Controlador — `ActividadLaboralController`

### `index()`
Carga todas las actividades con eager loading (`valorActividad.tipoActividad`, `lote`, `trabajador.cargo`), calcula los contadores de tarjetas (pendientes, confirmadas, rechazadas) y pasa también `$tarifas`, `$trabajadores` y `$lotes` para los modales de crear/editar.

### `store(Request $request)`
Valida y crea una nueva actividad. El `user_id` se asigna automáticamente desde `Auth::id()`. El `estado_confirmacion` siempre inicia como `'pendiente'`.

**Validaciones:**
- `fecha` — requerida, no puede ser futura (`before_or_equal:today`)
- `cantidad` — entero, mínimo 1
- `numero_pasada` — entero, mínimo 1

### `update(Request $request, ActividadLaboral $actividad)`
Solo permite editar actividades en estado `'pendiente'`. Las confirmadas o rechazadas son inmutables.

### `destroy(ActividadLaboral $actividad)`
Solo elimina actividades que no estén en estado `'confirmado'`.

### `confirmar(Request $request, ActividadLaboral $actividad)`
Cambia el `estado_confirmacion` a `confirmado`, `rechazado` o `pendiente`. Solo las actividades `confirmadas` son incluibles en liquidaciones de pago.

### `avanceLote(Request $request)`
Endpoint AJAX (`GET /actividades/avance-lote`). Recibe `lote_id`, `valor_actividad_id` y opcionalmente `excluir_id`. Devuelve JSON con el acumulado de hectáreas ya registradas para esa combinación, el total del lote y las restantes. Usado por el formulario de crear actividad para mostrar el progreso en tiempo real.

---

## Vista — `index.blade.php`

### Estructura
1. **Tarjetas de resumen** — contadores de pendientes, confirmadas y rechazadas
2. **Tabla DataTable** — listado de actividades con columnas: #, fecha, trabajador, actividad/tarifa, lote, cant., pasadas, v. unitario, subtotal, estado, acciones
3. **Modal `#modalCrear`** — formulario de nueva actividad con widget de avance de lote
4. **Modal `#modalEditar`** — formulario de edición (solo para actividades pendientes)

### Widget de avance de lote
Al seleccionar lote + tarifa en el formulario, aparece automáticamente una barra de progreso con:
- Hectáreas ya registradas vs total del lote
- Porcentaje completado (verde → ámbar → rojo según nivel)
- Advertencia si la cantidad ingresada supera las hectáreas disponibles

### Confirmación con advertencia de avance
Al dar clic en el botón ✓ (confirmar), en lugar del `swConfirm` genérico, el sistema calcula en Blade el avance del lote y muestra un SweetAlert2 con:
- **Si quedan hectáreas**: "Llevarás X ha de Y ha. Quedan Z ha pendientes."
- **Si se excede**: "Atención: registrarías X ha pero el lote solo tiene Y ha."
- Botones: **Cancelar** / **Sí, confirmar**

### JavaScript clave
| Función | Descripción |
|---------|-------------|
| `openModal(id)` | Abre un modal y dispara `actualizarAvanceLote` si es `modalCrear` |
| `calcularSubtotalModal()` | Recalcula subtotal en tiempo real al cambiar tarifa/cantidad/pasadas |
| `actualizarAvanceLote(prefijo, excluirId)` | Fetch a `/avance-lote` y actualiza widget de progreso |
| `verificarExceso(prefijo)` | Muestra advertencia ámbar si la cantidad supera el disponible |
| `confirmarConAvance(e, btn)` | Lee `data-*` del botón y muestra SweetAlert2 con info de avance |
| `openEditActividadModal(...)` | Rellena el modal de edición con datos del registro seleccionado |

---

## Flujo principal

```
Operario registra labor
    ↓
ActividadLaboral creada (estado: pendiente)
    ↓
Administrador revisa → botón ✓
    ↓
SweetAlert2 muestra avance del lote en el modal
    ↓
Admin confirma → estado: confirmado
    ↓
Actividad disponible para liquidación semanal
```

---

## Relación con otros módulos

- **Pagos/Liquidaciones** — el módulo de pagos consulta las actividades `confirmadas` para generar la nómina semanal. Los `DetallePago` guardan un snapshot de los valores al momento de liquidar.
- **Lotes** — el campo `tamano_hectareas` del lote es la referencia para el widget de avance.
- **Tarifas** — el `valor_unitario` de la tarifa seleccionada determina el subtotal.
