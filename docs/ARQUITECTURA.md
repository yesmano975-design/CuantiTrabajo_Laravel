# Arquitectura del Sistema — CuantiTrabajo

> Documento técnico que explica cómo está construido el sistema, cómo se conectan
> sus capas (MVC) y cómo se relaciona con la base de datos.

---

## Índice

1. [Stack tecnológico](#1-stack-tecnológico)
2. [Estructura de carpetas](#2-estructura-de-carpetas)
3. [Patrón MVC en Laravel](#3-patrón-mvc-en-laravel)
4. [Cómo fluye una petición (request lifecycle)](#4-cómo-fluye-una-petición-request-lifecycle)
5. [Base de datos — conexión y configuración](#5-base-de-datos--conexión-y-configuración)
6. [Diagrama de tablas y relaciones](#6-diagrama-de-tablas-y-relaciones)
7. [Módulos del sistema y sus rutas](#7-módulos-del-sistema-y-sus-rutas)
8. [Control de acceso por roles](#8-control-de-acceso-por-roles)
9. [Flujo completo: registrar y liquidar una actividad](#9-flujo-completo-registrar-y-liquidar-una-actividad)

---

## 1. Stack tecnológico

| Capa | Tecnología |
|---|---|
| Backend | PHP 8.3 + Laravel 13 |
| Frontend | Blade Templates + Tailwind CSS (CDN) |
| Base de datos | MySQL (producción con Laragon) |
| Alertas UI | SweetAlert2 |
| Iconos | Font Awesome 6 |
| Servidor local | Laragon (Apache + MySQL) |

---

## 2. Estructura de carpetas

```
CuantiTrabajo_Laravel/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/        ← Controladores (lógica de cada módulo)
│   │   └── Middleware/
│   │       └── CheckRol.php    ← Control de acceso por rol
│   │
│   ├── Models/                 ← Modelos Eloquent (representan las tablas)
│   └── Providers/
│
├── bootstrap/
│   └── app.php                 ← Registro de middlewares y configuración base
│
├── config/
│   └── database.php            ← Configuración de conexiones a BD
│
├── database/
│   ├── migrations/             ← Definición de la estructura de las tablas
│   └── seeders/
│       └── DatabaseSeeder.php  ← Datos iniciales (roles, cargos, admin)
│
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── sidebaradmin.blade.php  ← Layout principal (sidebar + topbar)
│       ├── auth/               ← Vistas de login
│       └── admin/              ← Vistas de cada módulo
│           ├── actividades/
│           ├── lotes/
│           ├── pagos/
│           ├── tarifas/
│           ├── tipo-actividades/
│           ├── trabajadores/
│           └── usuarios/
│
├── routes/
│   ├── web.php                 ← Definición de todas las rutas HTTP
│   └── auth.php                ← Rutas de login y logout
│
└── docs/
    └── ARQUITECTURA.md         ← Este archivo
```
request

---

## 3. Patrón MVC en Laravel

El sistema sigue el patrón **Modelo – Vista – Controlador (MVC)**. Cada capa tiene una responsabilidad clara:

```
PETICIÓN HTTP
     │
     ▼
┌──────────┐     ┌─────────────────┐     ┌──────────────┐
│  RUTAS   │────▶│  CONTROLADOR    │────▶│   MODELO     │
│ web.php  │     │  (Controller)   │◀────│  (Eloquent)  │
└──────────┘     └────────┬────────┘     └──────┬───────┘
                          │                      │
                          │ datos                │ SQL
                          ▼                      ▼
                   ┌────────────┐         ┌────────────┐
                   │   VISTA    │         │    BASE    │
                   │  (Blade)   │         │  DE DATOS  │
                   └────────────┘         └────────────┘
```

### Modelo (`app/Models/`)
- Cada archivo representa **una tabla** de la base de datos.
- Usa **Eloquent ORM**: en lugar de escribir SQL manualmente, se usan métodos PHP como `Trabajador::where('estado', 'activo')->get()`.
- Define las **relaciones** entre tablas (`belongsTo`, `hasMany`, `hasOne`).
- Puede tener **accessors**: atributos calculados que no están en la BD (ej: `$actividad->subtotal`).

### Vista (`resources/views/`)
- Archivos `.blade.php`: HTML mezclado con directivas de Laravel (`@foreach`, `@if`, `{{ $variable }}`).
- Todas las vistas del panel extienden el layout `layouts/sidebaradmin.blade.php` con `@extends`.
- El layout contiene el sidebar, la topbar y el contenedor principal. Cada vista solo define su contenido con `@section('content')`.

### Controlador (`app/Http/Controllers/`)
- Es el intermediario: recibe la petición, consulta el modelo, devuelve la vista.
- Valida los datos del formulario antes de guardar (`$request->validate([...])`).
- Redirige con mensajes de éxito/error usando `->with('success', '...')`.

---

## 4. Cómo fluye una petición (request lifecycle)

**Ejemplo: el usuario guarda un nuevo trabajador**

```
1. Usuario llena el formulario y hace clic en "Guardar"
        │
        ▼
2. El navegador envía POST /trabajadores
        │
        ▼
3. Laravel busca la ruta en routes/web.php
   → Route::resource('trabajadores', TrabajadorController::class)
        │
        ▼
4. Pasa por los middlewares del grupo:
   → 'auth'  → verifica que haya sesión activa
        │
        ▼
5. Llega a TrabajadorController@store()
   → Valida los campos del formulario
   → Si falla: devuelve al formulario con errores
   → Si pasa: crea el registro con Trabajador::create([...])
        │
        ▼
6. Eloquent traduce create() a:
   INSERT INTO trabajadores (...) VALUES (...)
        │
        ▼
7. El controlador redirige a la lista:
   return redirect()->route('trabajadores.index')->with('success', '...')
        │
        ▼
8. El navegador hace GET /trabajadores
   → TrabajadorController@index() consulta la BD
   → Pasa los datos a la vista
   → La vista renderiza el HTML con los trabajadores
   → SweetAlert2 muestra el mensaje de éxito (session flash)
```

---

## 5. Base de datos — conexión y configuración

### Dónde se configura

La conexión se define en **dos archivos**:

**Archivo 1: `.env`** (variables de entorno, no se sube al repositorio)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cuantitrabajo
DB_USERNAME=root
DB_PASSWORD=
```

**Archivo 2: `config/database.php`**
Lee las variables del `.env` con `env('DB_HOST', '127.0.0.1')`.
El valor después de la coma es el **valor por defecto** si la variable no existe en `.env`.

### Cómo Laravel se conecta

```
.env  →  config/database.php  →  Eloquent ORM  →  Base de Datos MySQL
```

1. Al arrancar, Laravel carga el `.env` con las credenciales.
2. `config/database.php` construye la configuración de conexión.
3. Eloquent usa esa configuración para cada consulta.
4. No se escribe SQL directamente: Eloquent lo genera internamente.

### Migraciones — cómo se crean las tablas

Las tablas no se crean manualmente en MySQL. Se crean ejecutando:

```bash
php artisan migrate
```

Laravel lee los archivos de `database/migrations/` en orden cronológico y ejecuta cada uno. Si se quiere volver a crear todo desde cero:

```bash
php artisan migrate:fresh --seed
```

El `--seed` también ejecuta `DatabaseSeeder.php` que inserta los datos iniciales (roles, cargos, usuario administrador).

---

## 6. Diagrama de tablas y relaciones

```
roles
├── id
├── nombre          ← 'administrador' | 'secretaria'
└── descripcion

users
├── id
├── rol_id ─────────────────────────────────────────► roles.id
├── nombre
├── email           ← credencial de login
├── password        ← bcrypt
└── estado

cargos
├── id
├── nombre          ← 'Fumigador', 'Tractorista', etc.
└── descripcion

trabajadores
├── id
├── cargo_id ───────────────────────────────────────► cargos.id
├── nombre
├── documento       ← único
└── estado          ← 'activo' | 'inactivo'

lotes
├── id
├── nombre
├── referencia      ← código único del terreno
└── tamano_hectareas

tipo_actividades
├── id
├── nombre          ← 'Fumigación', 'Poda', etc.
└── unidad_medida   ← 'horas' | 'dias' | 'hectareas'

valor_actividades  (tarifas)
├── id
├── tipo_actividad_id ──────────────────────────────► tipo_actividades.id
├── valor_unitario
├── fecha_inicio
├── fecha_fin       ← NULL = sin vencimiento
└── estado          ← 'activo' | 'inactivo'

actividad_laborals  ← tabla central del sistema
├── id
├── valor_actividad_id ─────────────────────────────► valor_actividades.id
├── lote_id ────────────────────────────────────────► lotes.id
├── trabajador_id ──────────────────────────────────► trabajadores.id
├── user_id ────────────────────────────────────────► users.id
├── fecha
├── cantidad
├── numero_pasada
├── observacion
└── estado_confirmacion  ← 'pendiente' | 'confirmado' | 'rechazado'

pagos
├── id
├── fecha_generacion
├── periodo_inicio   ← lunes de la semana
├── periodo_fin      ← sábado de la semana
├── total_pago
└── estado           ← 'pendiente' | 'pagado'

detalle_pagos
├── id
├── pago_id ────────────────────────────────────────► pagos.id
├── actividad_laboral_id ───────────────────────────► actividad_laborals.id
├── cantidad         ← copia del valor al momento de liquidar
├── valor_unitario   ← copia del valor al momento de liquidar
└── subtotal         ← cantidad × valor_unitario × numero_pasada

facturas
├── id
├── pago_id ────────────────────────────────────────► pagos.id
├── fecha_emision
└── numero_factura
```

### Resumen de relaciones

| Modelo | Relación | Con |
|---|---|---|
| `Rol` | tiene muchos | `User` |
| `User` | pertenece a | `Rol` |
| `Cargo` | tiene muchos | `Trabajador` |
| `Trabajador` | pertenece a | `Cargo` |
| `Trabajador` | tiene muchos | `ActividadLaboral` |
| `Lote` | tiene muchos | `ActividadLaboral` |
| `TipoActividad` | tiene muchos | `ValorActividad` |
| `ValorActividad` | pertenece a | `TipoActividad` |
| `ValorActividad` | tiene muchos | `ActividadLaboral` |
| `ActividadLaboral` | pertenece a | `Trabajador`, `Lote`, `ValorActividad`, `User` |
| `ActividadLaboral` | tiene muchos | `DetallePago` |
| `Pago` | tiene muchos | `DetallePago` |
| `Pago` | tiene uno | `Factura` |
| `DetallePago` | pertenece a | `Pago`, `ActividadLaboral` |

---

## 7. Módulos del sistema y sus rutas

| Módulo | URL base | Controlador | Acceso |
|---|---|---|---|
| Dashboard | `/dashboard` | `DashboardController` | Todos |
| Trabajadores | `/trabajadores` | `TrabajadorController` | Todos |
| Lotes | `/lotes` | `LoteController` | Todos |
| Actividades | `/actividades` | `ActividadLaboralController` | Todos |
| Pagos | `/pagos` | `PagoController` | Todos |
| Tipos de Actividad | `/tipo-actividades` | `TipoActividadController` | Solo Admin |
| Tarifas | `/tarifas` | `ValorActividadController` | Solo Admin |
| Usuarios | `/usuarios` | `UsuarioController` | Solo Admin |

Todas las rutas usan el patrón **resource** de Laravel, que genera automáticamente:

```
GET    /recurso           → index()   listar
GET    /recurso/create    → create()  mostrar formulario
POST   /recurso           → store()   guardar nuevo
GET    /recurso/{id}/edit → edit()    mostrar formulario edición
PUT    /recurso/{id}      → update()  guardar cambios
DELETE /recurso/{id}      → destroy() eliminar
```

---

## 8. Control de acceso por roles

### Cómo funciona

```
Petición HTTP
     │
     ▼
Middleware 'auth'         → ¿hay sesión? Si no → redirige al login
     │
     ▼
Middleware 'role:admin'   → ¿el rol del usuario está en la lista?
     │                       Si no → abort(403) página de acceso denegado
     ▼
Controlador
```

### Archivo clave: `app/Http/Middleware/CheckRol.php`

```php
// Forma de uso en rutas:
Route::middleware(['auth', 'role:administrador'])->group(function () {
    // Solo administradores llegan aquí
});
```

### Permisos por rol

| Sección | Administrador | Secretaria |
|---|:---:|:---:|
| Dashboard | ✅ | ✅ |
| Trabajadores | ✅ | ✅ |
| Lotes | ✅ | ✅ |
| Actividades | ✅ | ✅ |
| Liquidación Pagos | ✅ | ✅ |
| Tipos de Actividad | ✅ | ❌ |
| Tarifas y Valores | ✅ | ❌ |
| Usuarios y Roles | ✅ | ❌ |

El sidebar también oculta visualmente los ítems restringidos usando `@if(Auth::user()->rol->nombre === 'administrador')` en la vista del layout.

---

## 9. Flujo completo: registrar y liquidar una actividad

Este es el flujo principal del negocio, de punta a punta:

```
1. CONFIGURACIÓN (Admin)
   ┌─────────────────────────────────────────────────────────┐
   │ Crear Tipo de Actividad  →  "Fumigación" / hectáreas    │
   │ Crear Tarifa             →  $18.000 por hectárea        │
   │                             vigente desde 01/01/2025    │
   └─────────────────────────────────────────────────────────┘
                    │
                    ▼
2. REGISTRO DIARIO (Admin o Secretaria)
   ┌─────────────────────────────────────────────────────────┐
   │ POST /actividades                                        │
   │  trabajador: Jhonny Leche                               │
   │  lote: Bugangaí                                         │
   │  tarifa: Fumigación $18.000/ha                          │
   │  cantidad: 20 hectáreas / pasada: 1                     │
   │  → estado: PENDIENTE                                    │
   │  → subtotal: 20 × $18.000 × 1 = $360.000               │
   └─────────────────────────────────────────────────────────┘
                    │
                    ▼
3. VALIDACIÓN (Admin o Secretaria)
   ┌─────────────────────────────────────────────────────────┐
   │ PATCH /actividades/{id}/confirmar                        │
   │  estado_confirmacion: 'confirmado'                      │
   │  → La actividad queda disponible para liquidar          │
   └─────────────────────────────────────────────────────────┘
                    │
                    ▼
4. LIQUIDACIÓN SEMANAL (Admin o Secretaria)
   ┌─────────────────────────────────────────────────────────┐
   │ POST /pagos                                              │
   │  periodo: lunes 18/08/2025 → sábado 23/08/2025         │
   │  → Se crea registro en 'pagos' (cabecera)               │
   │  → Se crea registro en 'detalle_pagos' por actividad    │
   │  → El detalle guarda una copia del valor_unitario       │
   │     (para que cambios futuros de tarifa no lo afecten)  │
   │  → estado del pago: PENDIENTE                           │
   └─────────────────────────────────────────────────────────┘
                    │
                    ▼
5. PAGO EFECTIVO (Admin o Secretaria)
   ┌─────────────────────────────────────────────────────────┐
   │ PATCH /pagos/{id}/marcar-pagado                          │
   │  → estado del pago cambia a: PAGADO                     │
   │  → El pago ya no puede eliminarse ni modificarse        │
   └─────────────────────────────────────────────────────────┘
```

---

*Generado para el proyecto CuantiTrabajo — Laravel 13 / PHP 8.3*
