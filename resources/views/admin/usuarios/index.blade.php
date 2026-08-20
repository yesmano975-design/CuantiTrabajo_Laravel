@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Usuarios del Sistema')

@section('content')

{{-- Resumen de Métricas --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    {{-- Total --}}
    <div class="glass-card p-5 border-l-4 border-emerald-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Cuentas</span>
            <div class="text-3xl font-display font-black text-slate-800 mt-1">{{ $usuarios->count() }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-users-gear"></i>
        </div>
    </div>

    {{-- Activos --}}
    <div class="glass-card p-5 border-l-4 border-blue-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Usuarios Activos</span>
            <div class="text-3xl font-display font-black text-blue-600 mt-1">{{ $usuarios->where('estado','activo')->count() }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-user-check"></i>
        </div>
    </div>

    {{-- Inactivos --}}
    <div class="glass-card p-5 border-l-4 border-rose-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Cuentas Bloqueadas</span>
            <div class="text-3xl font-display font-black text-rose-600 mt-1">{{ $usuarios->where('estado','inactivo')->count() }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-user-slash"></i>
        </div>
    </div>
</div>

{{-- Tabla Principal --}}
<div class="glass-card overflow-hidden">
    {{-- Card Header --}}
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gradient-to-r from-slate-50/80 to-emerald-50/30">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-forest text-emerald-300 flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <h2 class="font-display font-bold text-xl text-slate-800">Listado de Usuarios</h2>
                <p class="text-xs text-slate-500">Administración de credenciales, roles de acceso y perfiles</p>
            </div>
        </div>
        <button type="button" onclick="openModal('modalCrear')" class="btn-primary-custom flex items-center gap-2">
            <i class="fas fa-user-plus"></i>
            <span>Nuevo Usuario</span>
        </button>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
    <div class="mx-6 mt-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center gap-2">
        <i class="fas fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mx-6 mt-4 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm flex items-center gap-2">
        <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
    </div>
    @endif

    {{-- Table Area --}}
    <div class="p-6">
        <div class="overflow-x-auto">
            <table id="usuariosTable" class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Usuario</th>
                        <th>Correo Electrónico</th>
                        <th>Rol de Acceso</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($usuarios as $usuario)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="font-bold text-slate-400 text-xs">#{{ $usuario->id }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 font-bold text-xs uppercase shadow-inner">
                                    {{ substr($usuario->nombre, 0, 1) }}{{ substr($usuario->apellido ?? '', 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">{{ $usuario->nombre }} {{ $usuario->apellido }}</div>
                                    <div class="text-[11px] text-slate-400">ID: {{ $usuario->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="font-medium text-slate-600">
                            <span class="font-mono text-xs">{{ $usuario->email }}</span>
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold {{ ($usuario->rol->nombre ?? '') === 'admin' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                <i class="fas fa-shield-halved text-[10px]"></i>
                                {{ ucfirst($usuario->rol->nombre ?? 'Sin Rol') }}
                            </span>
                        </td>
                        <td>
                            <span class="font-mono text-xs text-slate-600">{{ $usuario->telefono ?: '-' }}</span>
                        </td>
                        <td>
                            @if($usuario->estado === 'activo')
                                <span class="badge-active">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo
                                </span>
                            @else
                                <span class="badge-inactive">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Inactivo
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Editar --}}
                                <button type="button"
                                    onclick="openEditModal({{ $usuario->id }}, {{ $usuario->rol_id }}, '{{ addslashes($usuario->nombre) }}', '{{ addslashes($usuario->apellido) }}', '{{ $usuario->email }}', '{{ $usuario->telefono }}')"
                                    class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                    title="Editar Usuario">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                </button>

                                {{-- Toggle Estado --}}
                                <form action="{{ route('usuarios.toggleEstado', $usuario) }}"
                                      method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg {{ $usuario->estado === 'activo' ? 'bg-slate-100 text-slate-600 hover:bg-slate-200' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }} hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                        title="{{ $usuario->estado === 'activo' ? 'Desactivar Cuenta' : 'Activar Cuenta' }}"
                                        onclick="return swConfirm(this, '¿Cambiar el estado de este usuario?', 'question', 'Sí, cambiar')">
                                        <i class="fas fa-{{ $usuario->estado === 'activo' ? 'ban' : 'check' }} text-xs"></i>
                                    </button>
                                </form>

                                {{-- Eliminar --}}
                                <form action="{{ route('usuarios.destroy', $usuario) }}"
                                      method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                        title="Eliminar Cuenta"
                                        onclick="return swConfirm(this, '¿Eliminar este usuario permanentemente?', 'warning', 'Sí, eliminar')">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-slate-400">
                            <i class="fas fa-users text-3xl mb-2 block text-slate-300"></i>
                            No hay usuarios registrados en el sistema.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL CREAR --}}
{{-- ============================================================ --}}
<div id="modalCrear" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modalCrear')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-lg text-slate-800">Nuevo Usuario</h3>
                    <p class="text-xs text-slate-500">Crear una nueva cuenta de acceso al sistema</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalCrear')"
                class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors flex items-center justify-center">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        {{-- Form --}}
        <form id="formCrear" action="{{ route('usuarios.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            {{-- Errores --}}
            @if($errors->any() && old('_method') === null)
            <div class="px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Rol --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Rol de Acceso <span class="text-rose-500">*</span>
                    </label>
                    <select name="rol_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-sm text-slate-800 bg-white">
                        <option value="" disabled {{ old('rol_id') ? '' : 'selected' }}>Seleccionar rol...</option>
                        @foreach($roles as $rol)
                        <option value="{{ $rol->id }}" {{ old('rol_id') == $rol->id ? 'selected' : '' }}>
                            {{ ucfirst($rol->nombre) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Nombre --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Nombre <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required
                        placeholder="Ej: Juan"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-sm text-slate-800 placeholder-slate-400">
                </div>

                {{-- Apellido --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Apellido <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                    </label>
                    <input type="text" name="apellido" value="{{ old('apellido') }}"
                        placeholder="Ej: Pérez"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-sm text-slate-800 placeholder-slate-400">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Correo Electrónico <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="usuario@ejemplo.com"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-sm font-mono placeholder-slate-400">
                </div>

                {{-- Teléfono --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Teléfono <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                    </label>
                    <input type="text" name="telefono" value="{{ old('telefono') }}"
                        placeholder="Ej: 300 123 4567"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-sm font-mono placeholder-slate-400">
                </div>

                {{-- Contraseña --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Contraseña <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password" required minlength="6"
                        placeholder="Mínimo 6 caracteres"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-sm placeholder-slate-400">
                </div>

                {{-- Confirmar Contraseña --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Confirmar Contraseña <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" required
                        placeholder="Repetir contraseña"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-sm placeholder-slate-400">
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalCrear')"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition text-sm font-medium">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-floppy-disk"></i> Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL EDITAR --}}
{{-- ============================================================ --}}
<div id="modalEditar" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modalEditar')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    <i class="fas fa-pen-to-square"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-lg text-slate-800">Editar Usuario</h3>
                    <p class="text-xs text-slate-500">Modificar los datos del perfil y acceso</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalEditar')"
                class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors flex items-center justify-center">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        {{-- Form --}}
        <form id="formEditar" action="" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            {{-- Errores edición --}}
            @if($errors->any() && old('_method') === 'PUT')
            <div class="px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Rol --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Rol de Acceso <span class="text-rose-500">*</span>
                    </label>
                    <select id="edit_rol" name="rol_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm text-slate-800 bg-white">
                        @foreach($roles as $rol)
                        <option value="{{ $rol->id }}">{{ ucfirst($rol->nombre) }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Nombre --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Nombre <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="edit_nombre" name="nombre" required
                        placeholder="Ej: Juan"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm text-slate-800 placeholder-slate-400">
                </div>

                {{-- Apellido --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Apellido <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                    </label>
                    <input type="text" id="edit_apellido" name="apellido"
                        placeholder="Ej: Pérez"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm text-slate-800 placeholder-slate-400">
                </div>

                {{-- Email (disabled) --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Correo Electrónico
                    </label>
                    <input type="email" id="edit_email" disabled
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-400 text-sm font-mono cursor-not-allowed">
                    <p class="text-xs text-slate-400 mt-1 flex items-center gap-1">
                        <i class="fas fa-lock text-[10px]"></i> No se puede modificar el correo electrónico
                    </p>
                </div>

                {{-- Teléfono --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Teléfono <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                    </label>
                    <input type="text" id="edit_telefono" name="telefono"
                        placeholder="Ej: 300 123 4567"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm font-mono placeholder-slate-400">
                </div>

                {{-- Nueva Contraseña --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Nueva Contraseña <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                    </label>
                    <input type="password" name="password" minlength="6"
                        placeholder="Dejar en blanco para mantener"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm placeholder-slate-400">
                </div>

                {{-- Confirmar Contraseña --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Confirmar Contraseña <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                    </label>
                    <input type="password" name="password_confirmation"
                        placeholder="Repetir nueva contraseña"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm placeholder-slate-400">
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalEditar')"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition text-sm font-medium">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-floppy-disk"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<script>
// ── Modal helpers ──────────────────────────────────────────────
function openModal(id) {
    const el = document.getElementById(id);
    el.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    const el = document.getElementById(id);
    el.classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeModal('modalCrear');
        closeModal('modalEditar');
    }
});

// ── Open Edit Modal ────────────────────────────────────────────
function openEditModal(id, rol_id, nombre, apellido, email, telefono) {
    document.getElementById('formEditar').action = '/usuarios/' + id;
    document.getElementById('edit_rol').value      = rol_id;
    document.getElementById('edit_nombre').value   = nombre;
    document.getElementById('edit_apellido').value = apellido;
    document.getElementById('edit_email').value    = email;
    document.getElementById('edit_telefono').value = telefono;
    openModal('modalEditar');
}

// ── DataTable ──────────────────────────────────────────────────
$(document).ready(function () {
    if ($('#usuariosTable tbody tr').length > 1 || !$('#usuariosTable tbody tr td[colspan]').length) {
        $('#usuariosTable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Buscar usuario...",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ usuarios",
                infoEmpty: "Mostrando 0 registros",
                infoFiltered: "(filtrado de _MAX_ totales)",
                paginate: { first: "«", previous: "‹", next: "›", last: "»" }
            }
        });
    }
});

// ── Reabrir modal crear si hay errores de validación ───────────
@if($errors->any() && old('_method') === null)
    openModal('modalCrear');
@endif
</script>
@endsection
