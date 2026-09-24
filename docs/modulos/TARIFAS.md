# Módulo: Tarifas y Tipos de Actividad

Define el catálogo de labores agrícolas disponibles y su valor económico por unidad.
Son la base para calcular los subtotales de cada actividad registrada.

---

## Archivos involucrados

| Tipo | Ruta |
|------|------|
| Controlador Tipos | `app/Http/Controllers/TipoActividadController.php` |
| Controlador Tarifas | `app/Http/Controllers/ValorActividadController.php` |
| Modelos | `app/Models/TipoActividad.php`, `app/Models/ValorActividad.php` |
| Vista Tipos | `resources/views/admin/tipo-actividades/index.blade.php` |
| Vista Tarifas | `resources/views/admin/tarifas/index.blade.php` |
| Acceso | Solo administrador — `middleware(['auth', 'role:administrador', 'no-cache'])` |

---

## Rutas

```
GET    /tipo-actividades                        → index()   tipo-actividades.index
POST   /tipo-actividades                        → store()   tipo-actividades.store
PUT    /tipo-actividades/{tipo_actividad}       → update()  tipo-actividades.update
DELETE /tipo-actividades/{tipo_actividad}       → destroy() tipo-actividades.destroy

GET    /tarifas                                 → index()   tarifas.index
POST   /tarifas                                 → store()   tarifas.store
PUT    /tarifas/{tarifa}                        → update()  tarifas.update
DELETE /tarifas/{tarifa}                        → destroy() tarifas.destroy
```

> `create`, `edit` y `show` excluidos. Formularios son modales inline.
> Solo el **administrador** puede acceder a estos módulos.

---

## Modelos

### `TipoActividad`
**Tabla:** `tipo_actividades`

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `nombre` | string | Nombre de la labor (ej: "Rastra", "Lampley") |
| `unidad_medida` | string | Unidad de cobro (ej: "hectareas", "bultos") |
| `descripcion` | string | Descripción opcional |

Relaciones: `hasMany(ValorActividad)`

### `ValorActividad` (Tarifa)
**Tabla:** `valor_actividades`

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `tipo_actividad_id` | FK | Tipo de labor al que aplica |
| `valor_unitario` | decimal | Precio por unidad de medida |
| `fecha_inicio` | date | Inicio de vigencia |
| `fecha_fin` | date | Fin de vigencia (null = sin vencimiento) |
| `estado` | string | `activo` / `inactivo` |

Relaciones: `belongsTo(TipoActividad)`, `hasMany(ActividadLaboral)`

---

## Relación entre los dos modelos

```
TipoActividad (Rastra)
    └── ValorActividad ($12.000/ha, vigente desde 2025-01-01)
    └── ValorActividad ($10.000/ha, vigente 2024-01-01 hasta 2024-12-31)
```

Un tipo de actividad puede tener múltiples tarifas históricas.
En el formulario de actividades solo aparecen las tarifas **activas y vigentes hoy**.

---

## Fórmula de subtotal

```
subtotal = cantidad × valor_unitario × numero_pasada
```

Esta fórmula se aplica en:
- JS en tiempo real en el formulario de actividades
- Accessor `getSubtotalAttribute()` en el modelo `ActividadLaboral`
- Cálculo PHP en `PagoController` al generar la liquidación

---

## Vistas

Ambas vistas (`tipo-actividades/index` y `tarifas/index`) siguen el mismo patrón:
- Tabla DataTable con los registros
- Modal de crear (inline)
- Modal de editar (inline)

En `tarifas/index` adicionalmente se muestra la vigencia de cada tarifa con un badge de estado visual (activa / inactiva / vencida).

---

## Relación con otros módulos

- **Actividades** — el selector de tarifa en el formulario solo muestra las `ValorActividad` con `estado = 'activo'` y dentro del rango de fechas actual. Al cambiar la tarifa, el subtotal se recalcula en tiempo real.
- **Pagos** — el `valor_unitario` se copia como snapshot al `DetallePago` en el momento de liquidar, garantizando que cambios futuros de precio no alteren liquidaciones históricas.
