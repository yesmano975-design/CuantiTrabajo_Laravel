@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Lotes de Cultivo')

@section('content')

{{-- Resumen de Métricas --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
    {{-- Total Lotes --}}
    <div class="glass-card p-5 border-l-4 border-cyan-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Lotes Registrados</span>
            <div class="text-3xl font-display font-black text-slate-800 mt-1">{{ $lotes->count() }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-map-location-dot"></i>
        </div>
    </div>

    {{-- Total Hectáreas --}}
    <div class="glass-card p-5 border-l-4 border-emerald-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Área Total Administrada</span>
            <div class="text-3xl font-display font-black text-emerald-600 mt-1">{{ number_format($totalHectareas, 2) }} <span class="text-base font-normal text-slate-500">ha</span></div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-seedling"></i>
        </div>
    </div>
</div>

{{-- Tabla Principal --}}
<div class="glass-card overflow-hidden">
    {{-- Card Header --}}
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gradient-to-r from-slate-50/80 to-cyan-50/30">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-forest text-cyan-300 flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-map-location-dot"></i>
            </div>
            <div>
                <h2 class="font-display font-bold text-xl text-slate-800">Listado de Lotes</h2>
                <p class="text-xs text-slate-500">Control de parcelas, tamaño en hectáreas y actividades vinculadas</p>
            </div>
        </div>
        <a href="{{ route('lotes.create') }}" class="btn-primary-custom flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Nuevo Lote</span>
        </a>
    </div>

    {{-- Table Area --}}
    <div class="p-6">
        <div class="overflow-x-auto">
            <table id="lotesTable" class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lote / Nombre</th>
                        <th>Referencia</th>
                        <th>Ubicación</th>
                        <th>Extensión (ha)</th>
                        <th>Actividades Registradas</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($lotes as $lote)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="font-bold text-slate-400 text-xs">#{{ $lote->id }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-cyan-50 border border-cyan-200 text-cyan-700 flex items-center justify-center text-sm font-bold shadow-inner">
                                    <i class="fas fa-map-pin"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">{{ $lote->nombre }}</div>
                                    <div class="text-xs text-slate-400">{{ $lote->ubicacion ?: 'Sin coordenadas especificadas' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="font-mono bg-slate-100 px-2 py-1 rounded text-xs text-slate-600 border border-slate-200 font-semibold">
                                {{ $lote->referencia }}
                            </span>
                        </td>
                        <td class="text-slate-600 font-medium">
                            {{ $lote->ubicacion ?: '-' }}
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="fas fa-vector-square text-[10px]"></i>
                                {{ number_format($lote->tamano_hectareas, 2) }} ha
                            </span>
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                <i class="fas fa-clipboard-list text-[10px]"></i>
                                {{ $lote->actividadesLaborales()->count() }} labores
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Editar --}}
                                <a href="{{ route('lotes.edit', $lote) }}"
                                   class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                   title="Editar Lote">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                </a>

                                {{-- Eliminar --}}
                                <form action="{{ route('lotes.destroy', $lote) }}"
                                      method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                        title="Eliminar Lote"
                                        onclick="return swConfirm(this, '¿Eliminar este lote?', 'warning', 'Sí, eliminar')"
                                        {{ $lote->actividadesLaborales()->count() > 0 ? 'disabled title=No_se_puede_eliminar_tiene_actividades' : '' }}>
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-slate-400">
                            <i class="fas fa-map-location-dot text-3xl mb-2 block text-slate-300"></i>
                            No hay lotes registrados actualmente.
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
    if ($('#lotesTable tbody tr').length > 1 || !$('#lotesTable tbody tr td[colspan]').length) {
        $('#lotesTable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Buscar lote...",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ lotes",
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
