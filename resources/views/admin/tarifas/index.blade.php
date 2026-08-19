@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Tarifas')

@section('content')

{{-- Tabla Principal --}}
<div class="glass-card overflow-hidden">
    {{-- Card Header --}}
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gradient-to-r from-slate-50/80 to-emerald-50/30">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-forest text-emerald-300 flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div>
                <h2 class="font-display font-bold text-xl text-slate-800">Tarifas por Actividad</h2>
                <p class="text-xs text-slate-500">Valores unitarios por jornal, hectárea o unidad de labor agrícola</p>
            </div>
        </div>
        <a href="{{ route('tarifas.create') }}" class="btn-primary-custom flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Nueva Tarifa</span>
        </a>
    </div>

    {{-- Table Area --}}
    <div class="p-6">
        <div class="overflow-x-auto">
            <table id="tarifasTable" class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tipo de Actividad</th>
                        <th>Unidad de Medida</th>
                        <th class="text-right">Valor Unitario</th>
                        <th>Vigencia Desde</th>
                        <th>Vigencia Hasta</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tarifas as $tarifa)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="font-bold text-slate-400 text-xs">#{{ $tarifa->id }}</td>
                        <td>
                            <div class="font-bold text-slate-800">{{ $tarifa->tipoActividad->nombre ?? '-' }}</div>
                            <div class="text-xs text-slate-400">{{ $tarifa->tipoActividad->descripcion ?: 'Sin descripción adicional' }}</div>
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                <i class="fas fa-scale-unbalanced text-[10px] text-slate-400"></i>
                                {{ $tarifa->tipoActividad->unidad_medida ?? '-' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <span class="font-display font-black text-emerald-700 text-base font-mono">
                                ${{ number_format($tarifa->valor_unitario, 2) }}
                            </span>
                        </td>
                        <td class="font-medium text-slate-600">
                            {{ $tarifa->fecha_inicio->format('d/m/Y') }}
                        </td>
                        <td class="font-medium text-slate-600">
                            {{ $tarifa->fecha_fin ? $tarifa->fecha_fin->format('d/m/Y') : 'Indefinida' }}
                        </td>
                        <td>
                            @if($tarifa->estado === 'activo')
                                <span class="badge-active">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activa
                                </span>
                            @else
                                <span class="badge-inactive">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Inactiva
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Editar --}}
                                <a href="{{ route('tarifas.edit', $tarifa) }}"
                                   class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                   title="Editar Tarifa">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                </a>

                                {{-- Eliminar --}}
                                <form action="{{ route('tarifas.destroy', $tarifa) }}"
                                      method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                        title="Eliminar Tarifa"
                                        onclick="return swConfirm(this, '¿Eliminar esta tarifa?', 'warning', 'Sí, eliminar')">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-10 text-slate-400">
                            <i class="fas fa-dollar-sign text-3xl mb-2 block text-slate-300"></i>
                            No hay tarifas configuradas en el sistema.
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
    if ($('#tarifasTable tbody tr').length > 1 || !$('#tarifasTable tbody tr td[colspan]').length) {
        $('#tarifasTable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Buscar tarifa...",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ tarifas",
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
