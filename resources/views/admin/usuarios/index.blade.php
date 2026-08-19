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
        <a href="{{ route('usuarios.create') }}" class="btn-primary-custom flex items-center gap-2">
            <i class="fas fa-user-plus"></i>
            <span>Nuevo Usuario</span>
        </a>
    </div>

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
                                <a href="{{ route('usuarios.edit', $usuario) }}"
                                   class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                   title="Editar Usuario">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                </a>

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

@endsection

@section('js')
<script>
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
                paginate: {
                    first: "«",
                    previous: "‹",
                    next: "›",
                    last: "»"
                }
            }
        });
    }
});
</script>
@endsection
