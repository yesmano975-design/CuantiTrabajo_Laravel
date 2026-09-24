# Módulo: Dashboard

Vista general del sistema. Muestra indicadores clave del estado actual
de la operación agrícola: actividades pendientes, trabajadores activos,
lotes registrados y totales de la semana.

---

## Archivos involucrados

| Tipo | Ruta |
|------|------|
| Controlador | `app/Http/Controllers/DashboardController.php` |
| Vista | `resources/views/admin/dashboard.blade.php` |
| Layout | `resources/views/layouts/sidebaradmin.blade.php` |
| Rutas | `routes/web.php` — `GET /dashboard` → `dashboard` |

---

## Controlador — `DashboardController`

### `index()`
Consulta los contadores principales y los pasa a la vista:

| Variable | Descripción |
|----------|-------------|
| `$trabajadoresActivos` | Cantidad de trabajadores con `estado = 'activo'` |
| `$lotesRegistrados` | Total de lotes en el sistema |
| `$actividadesPendientes` | Actividades con `estado_confirmacion = 'pendiente'` |
| `$actividadesConfirmadas` | Actividades con `estado_confirmacion = 'confirmado'` |
| `$totalSemana` | Suma de subtotales de actividades confirmadas de la semana actual |
| `$pagosGenerados` | Cantidad de pagos en estado `pendiente` (generados pero no desembolsados) |

---

## Vista — `dashboard.blade.php`

### Tarjetas de resumen
Muestra los indicadores en tarjetas visuales con íconos y colores diferenciados:
- Trabajadores activos
- Lotes registrados
- Actividades por confirmar
- Total estimado de la semana

### Acceso rápido
Links directos a los módulos más usados: Nueva Actividad, Ver Actividades, Ver Pagos.

---

## Layout compartido — `sidebaradmin.blade.php`

Todas las vistas del panel admin extienden este layout con `@extends('layouts.sidebaradmin')`.

### Componentes del layout
- **Sidebar** — navegación lateral con menú filtrado por rol
- **Topbar** — breadcrumb, fecha actual, botón "Nueva Actividad" y menú de usuario
- **Paleta de colores** — definida en `tailwind.config` inline:
  - `brand` — verde campo (primario)
  - `forest` — verde selva oscuro (sidebar, gradientes)
  - `harvest` — ámbar dorado (acentos, badges pendientes)
  - `soil` — marrón tierra (uso decorativo)
- **SweetAlert2** — función global `swConfirm()` para confirmaciones de acciones
- **DataTables** — integración con jQuery para tablas interactivas

### Visibilidad por rol en el sidebar
```
Administrador: ve todo
Secretaria: no ve Usuarios, Tipos de Actividad ni Tarifas
```

---

## Relación con otros módulos

El dashboard no modifica datos — es solo lectura. Sirve como punto de entrada
y resumen de la actividad del sistema. Todos los enlaces del sidebar llevan
a los módulos especializados.
