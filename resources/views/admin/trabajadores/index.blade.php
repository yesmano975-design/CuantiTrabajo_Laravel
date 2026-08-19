@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Trabajadores')

@section('content')

{{-- Resumen de Métricas --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    {{-- Total --}}
    <div class="glass-card p-5 border-l-4 border-emerald-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Operarios</span>
            <div class="text-3xl font-display font-black text-slate-800 mt-1">{{ $trabajadores->count() }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-users"></i>
        </div>
    </div>

    {{-- Activos --}}
    <div class="glass-card p-5 border-l-4 border-blue-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Activos en Campo</span>
            <div class="text-3xl font-display font-black text-blue-600 mt-1">{{ $trabajadores->where('estado','activo')->count() }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-user-check"></i>
        </div>
    </div>

    {{-- Inactivos --}}
    <div class="glass-card p-5 border-l-4 border-rose-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Inactivos / Pausados</span>
            <div class="text-3xl font-display font-black text-rose-600 mt-1">{{ $trabajadores->where('estado','inactivo')->count() }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-user-times"></i>
        </div>
    </div>

    {{-- Cargos --}}
    <div class="glass-card p-5 border-l-4 border-amber-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Cargos Asignados</span>
            <div class="text-3xl font-display font-black text-amber-600 mt-1">{{ $cargos->count() }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-id-badge"></i>
        </div>
    </div>
</div>

{{-- Tabla Principal --}}
<div class="glass-card overflow-hidden">
    {{-- Card Header --}}
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gradient-to-r from-slate-50/80 to-emerald-50/30">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-forest text-emerald-300 flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-hard-hat"></i>
            </div>
            <div>
                <h2 class="font-display font-bold text-xl text-slate-800">Listado de Trabajadores</h2>
                <p class="text-xs text-slate-500">Gestión de personal de campo, cargos y estado operativo</p>
            </div>
        </div>
        <a href="{{ route('trabajadores.create') }}" class="btn-primary-custom flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Nuevo Trabajador</span>
        </a>
    </div>

    {{-- Table Area --}}
    <div class="p-6">
        <div class="overflow-x-auto">
            <table id="trabajadoresTable" class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Trabajador</th>
                        <th>Documento</th>
                        <th>Cargo</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($trabajadores as $trabajador)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="font-bold text-slate-400 text-xs">#{{ $trabajador->id }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 font-bold text-xs uppercase shadow-inner">
                                    {{ substr($trabajador->nombre, 0, 1) }}{{ substr($trabajador->apellido ?? '', 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">{{ $trabajador->nombre }} {{ $trabajador->apellido }}</div>
                                    <div class="text-xs text-slate-400">{{ $trabajador->correo ?: 'Sin correo' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="font-semibold text-slate-700">
                            <span class="font-mono bg-slate-100 px-2 py-1 rounded text-xs text-slate-600 border border-slate-200">
                                {{ $trabajador->documento }}
                            </span>
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="fas fa-briefcase text-[10px]"></i>
                                {{ $trabajador->cargo->nombre ?? 'Sin Cargo' }}
                            </span>
                        </td>
                        <td>
                            <div class="text-xs space-y-0.5">
                                @if($trabajador->telefono)
                                <div class="text-slate-600 flex items-center gap-1.5 font-mono">
                                    <i class="fas fa-phone text-slate-400 text-[10px]"></i> {{ $trabajador->telefono }}
                                </div>
                                @else
                                <span class="text-slate-400 italic text-xs">Sin teléfono</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($trabajador->estado === 'activo')
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
                                <a href="{{ route('trabajadores.edit', $trabajador) }}"
                                   class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                   title="Editar">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                </a>

                                {{-- Toggle Estado --}}
                                <form action="{{ route('trabajadores.toggleEstado', $trabajador) }}"
                                      method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg {{ $trabajador->estado === 'activo' ? 'bg-slate-100 text-slate-600 hover:bg-slate-200' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }} hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                        title="{{ $trabajador->estado === 'activo' ? 'Desactivar Trabajador' : 'Activar Trabajador' }}"
                                        onclick="return swConfirm(this, '¿Cambiar el estado de este trabajador?', 'question', 'Sí, cambiar')">
                                        <i class="fas fa-{{ $trabajador->estado === 'activo' ? 'user-slash' : 'user-check' }} text-xs"></i>
                                    </button>
                                </form>

                                {{-- Eliminar --}}
                                <form action="{{ route('trabajadores.destroy', $trabajador) }}"
                                      method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                        title="Eliminar"
                                        onclick="return swConfirm(this, '¿Eliminar este trabajador permanentemente?', 'warning', 'Sí, eliminar')">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-slate-400">
                            <i class="fas fa-folder-open text-3xl mb-2 block text-slate-300"></i>
                            No hay trabajadores registrados en la base de datos.
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
    if ($('#trabajadoresTable tbody tr').length > 1 || !$('#trabajadoresTable tbody tr td[colspan]').length) {
        $('#trabajadoresTable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Buscar trabajador...",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ trabajadores",
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
