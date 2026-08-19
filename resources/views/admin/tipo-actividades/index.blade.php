@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Tipos de Actividad')

@section('content')

{{-- Tabla Principal --}}
<div class="glass-card overflow-hidden">
    {{-- Card Header --}}
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gradient-to-r from-slate-50/80 to-emerald-50/30">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-forest text-emerald-300 flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-tags"></i>
            </div>
            <div>
                <h2 class="font-display font-bold text-xl text-slate-800">Catálogo de Actividades</h2>
                <p class="text-xs text-slate-500">Definición de conceptos de labor y sus unidades métricas de liquidación</p>
            </div>
        </div>
        <a href="{{ route('tipo-actividades.create') }}" class="btn-primary-custom flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Nuevo Tipo</span>
        </a>
    </div>

    {{-- Table Area --}}
    <div class="p-6">
        <div class="overflow-x-auto">
            <table id="tiposTable" class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre de la Labor</th>
                        <th>Descripción</th>
                        <th>Unidad de Medida</th>
                        <th>Tarifas Asociadas</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tipos as $tipo)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="font-bold text-slate-400 text-xs">#{{ $tipo->id }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center text-sm font-bold shadow-inner">
                                    <i class="fas fa-hand-holding-seedling"></i>
                                </div>
                                <span class="font-bold text-slate-800">{{ $tipo->nombre }}</span>
                            </div>
                        </td>
                        <td class="text-slate-600 font-medium">
                            {{ $tipo->descripcion ?: '-' }}
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                                <i class="fas fa-ruler text-[10px] text-slate-400"></i>
                                {{ $tipo->unidad_medida }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="fas fa-coins text-[10px]"></i>
                                    {{ $tipo->valor_actividades_count }} tarifa(s)
                                </span>
                                <a href="{{ route('tarifas.create') }}?tipo={{ $tipo->id }}"
                                   class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-all flex items-center justify-center shadow-sm"
                                   title="Crear tarifa para este tipo">
                                    <i class="fas fa-plus text-xs"></i>
                                </a>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Editar --}}
                                <a href="{{ route('tipo-actividades.edit', $tipo) }}"
                                   class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                   title="Editar Tipo">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                </a>

                                {{-- Eliminar --}}
                                <form action="{{ route('tipo-actividades.destroy', $tipo) }}"
                                      method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                        title="Eliminar Tipo"
                                        onclick="return swConfirm(this, '¿Eliminar este tipo de actividad?', 'warning', 'Sí, eliminar')">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-slate-400">
                            <i class="fas fa-tags text-3xl mb-2 block text-slate-300"></i>
                            No hay tipos de actividad registrados.
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
    if ($('#tiposTable tbody tr').length > 1 || !$('#tiposTable tbody tr td[colspan]').length) {
        $('#tiposTable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Buscar tipo de actividad...",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ tipos",
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
