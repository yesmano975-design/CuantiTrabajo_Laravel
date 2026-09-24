# Módulo: Usuarios y Roles

Gestión de las cuentas de acceso al panel administrativo.
Define quién puede entrar al sistema y con qué nivel de permisos.

---

## Archivos involucrados

| Tipo | Ruta |
|------|------|
| Controlador | `app/Http/Controllers/UsuarioController.php` |
| Controlador Auth | `app/Http/Controllers/Auth/LoginController.php` |
| Modelos | `app/Models/User.php`, `app/Models/Rol.php` |
| Vista principal | `resources/views/admin/usuarios/index.blade.php` |
| Vista login | `resources/views/auth/login.blade.php` |
| Middleware roles | `app/Http/Middleware/CheckRol.php` |
| Rutas | `routes/web.php` — group `middleware(['auth', 'role:administrador', 'no-cache'])` |

---

## Rutas

```
GET    /usuarios                              → index()       usuarios.index
POST   /usuarios                              → store()       usuarios.store
PUT    /usuarios/{usuario}                    → update()      usuarios.update
DELETE /usuarios/{usuario}                    → destroy()     usuarios.destroy
PATCH  /usuarios/{usuario}/toggle-estado     → toggleEstado() usuarios.toggleEstado
```

> Solo el **administrador** puede acceder. La secretaria no ve este módulo.
> `create`, `edit` y `show` excluidos del resource. Formularios son modales inline.

---

## Modelos

### `User`
**Tabla:** `users`

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `rol_id` | FK | Rol asignado (→ `roles`) |
| `nombre` | string | Nombre del usuario |
| `apellido` | string | Apellido (opcional) |
| `email` | string | Correo único — credencial de acceso |
| `password` | string | Hash bcrypt de la contraseña |
| `telefono` | string | Teléfono (opcional) |
| `estado` | string | `activo` / `inactivo` |

Relaciones: `belongsTo(Rol)`

### `Rol`
**Tabla:** `roles`

| Valor | Descripción |
|-------|-------------|
| `administrador` | Acceso total — CRUD de todos los módulos incluyendo usuarios, tarifas y tipos de actividad |
| `secretaria` | Acceso a dashboard, trabajadores, lotes, actividades y pagos. No puede gestionar usuarios ni catálogos |

---

## Middleware — `CheckRol`

Registrado como alias `role` en `bootstrap/app.php`.

```php
// Uso en rutas
Route::middleware(['auth', 'role:administrador'])->group(function () { ... });
```

Si el usuario autenticado no tiene el rol requerido, devuelve un `abort(403)`.

---

## Autenticación — `LoginController`

Maneja el login con `Auth::attempt()`. Tras autenticar redirige al dashboard.
El logout destruye la sesión y redirige al login.

### Vista `login.blade.php`
Diseño de dos columnas:
- **Izquierda** — carrusel de imágenes con textos rotativos sobre el sistema
- **Derecha** — formulario de email + contraseña con toggle de visibilidad

---

## Controlador — `UsuarioController`

### `index()`
Carga todos los usuarios con `with('rol')` ordenados por nombre.
Pasa `$roles` para los modales.

### `store(Request $request)`
Valida y crea usuario. La contraseña se hashea con `Hash::make()`.
Requiere confirmación de contraseña (`password_confirmation`).
Estado inicial: `activo`.

### `update(Request $request, User $usuario)`
La contraseña es opcional en edición — solo se actualiza si se envía.
No se puede cambiar el propio rol estando logueado (protección implícita).

### `toggleEstado(User $usuario)`
Alterna `activo` ↔ `inactivo` sin formulario.

---

## Relación con otros módulos

- **Actividades** — el `user_id` de cada actividad registra qué usuario del sistema la creó (auditoría).
- **Sidebar** — el rol del usuario autenticado controla qué secciones del menú son visibles (`@if(Auth::user()->rol->nombre === 'administrador')`).
