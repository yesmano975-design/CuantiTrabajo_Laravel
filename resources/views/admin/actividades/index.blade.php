@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Actividades Laborales')

@section('content')

{{-- Resumen de Métricas --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    {{-- Pendientes --}}
    <div class="glass-card p-5 border-l-4 border-amber-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Por Confirmar</span>
            <div class="text-3xl font-display font-black text-amber-600 mt-1">{{ $pendientes }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-clock"></i>
        </div>
    </div>

    {{-- Confirmadas --}}
    <div class="glass-card p-5 border-l-4 border-emerald-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Confirmadas (Válidas)</span>
            <div class="text-3xl font-display font-black text-emerald-600 mt-1">{{ $confirmadas }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-circle-check"></i>
        </div>
    </div>

    {{-- Rechazadas --}}
    <div class="glass-card p-5 border-l-4 border-rose-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Rechazadas / Anuladas</span>
            <div class="text-3xl font-display font-black text-rose-600 mt-1">{{ $rechazadas }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-circle-xmark"></i>
        </div>
    </div>
</div>

{{-- Tabla Principal --}}
<div class="glass-card overflow-hidden">
    {{-- Card Header --}}
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gradient-to-r from-slate-50/80 to-emerald-50/30">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-forest text-emerald-300 flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div>
                <h2 class="font-display font-bold text-xl text-slate-800">Registro Diario de Actividades</h2>
                <p class="text-xs text-slate-500">Supervisión de labores, pasadas realizadas y cálculo de subtotales</p>
            </div>
        </div>
        <a href="{{ route('actividades.create') }}" class="btn-primary-custom flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Nueva Actividad</span>
        </a>
    </div>

    {{-- Table Area --}}
    <div class="p-6">
        <div class="overflow-x-auto">
            <table id="actividadesTable" class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha</th>
                        <th>Trabajador</th>
                        <th>Actividad / Tarifa</th>
                        <th>Lote</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-center">Pasadas</th>
                        <th class="text-right">V. Unitario</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($actividades as $act)
                    @php
                        $valorUnit = $act->valorActividad->valor_unitario ?? 0;
                        $subtotal  = $act->cantidad * $valorUnit * $act->numero_pasada;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="font-bold text-slate-400 text-xs">#{{ $act->id }}</td>
                        <td class="font-semibold text-slate-700 whitespace-nowrap">
                            <i class="fas fa-calendar-day text-slate-400 text-xs mr-1"></i>
                            {{ $act->fecha->format('d/m/Y') }}
                        </td>
                        <td>
                            <div class="font-bold text-slate-800">{{ $act->trabajador->nombre ?? '-' }} {{ $act->trabajador->apellido ?? '' }}</div>
                            <div class="text-xs text-emerald-700 font-medium">{{ $act->trabajador->cargo->nombre ?? 'Sin cargo' }}</div>
                        </td>
                        <td>
                            <div class="font-medium text-slate-800">{{ $act->valorActividad->tipoActividad->nombre ?? '-' }}</div>
                            <div class="text-[11px] text-slate-400">Unidad: {{ $act->valorActividad->tipoActividad->unidad_medida ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1 font-semibold text-slate-700 text-xs">
                                <i class="fas fa-map-pin text-emerald-600 text-[10px]"></i>
                                {{ $act->lote->nombre ?? '-' }}
                            </span>
                            <span class="block text-[10px] text-slate-400 font-mono">{{ $act->lote->referencia ?? '' }}</span>
                        </td>
                        <td class="text-center font-bold text-slate-700">
                            {{ $act->cantidad }}
                        </td>
                        <td class="text-center font-bold text-slate-700">
                            <span class="bg-slate-100 px-2 py-0.5 rounded text-xs border border-slate-200">
                                {{ $act->numero_pasada }}
                            </span>
                        </td>
                        <td class="text-right font-mono text-xs text-slate-600">
                            ${{ number_format($valorUnit, 0, ',', '.') }}
                        </td>
                        <td class="text-right font-mono font-bold text-emerald-700">
                            ${{ number_format($subtotal, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @if($act->estado_confirmacion === 'confirmado')
                                <span class="badge-active">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Confirmado
                                </span>
                            @elseif($act->estado_confirmacion === 'pendiente')
                                <span class="badge-pending">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pendiente
                                </span>
                            @else
                                <span class="badge-inactive">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Rechazado
                                </span>
                            @endif
                        </td>
                        <td class="text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1">
                                
                                {{-- Editar (solo pendientes) --}}
                                @if($act->estado_confirmacion === 'pendiente')
                                <a href="{{ route('actividades.edit', $act) }}"
                                   class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition-all flex items-center justify-center shadow-sm"
                                   title="Editar Actividad">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                @endif

                                {{-- Confirmar --}}
                                @if($act->estado_confirmacion !== 'confirmado')
                                <form action="{{ route('actividades.confirmar', $act) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="estado_confirmacion" value="confirmado">
                                    <button type="submit"
                                        class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-all flex items-center justify-center shadow-sm"
                                        title="Aprobar y Confirmar"
                                        onclick="return swConfirm(this, '¿Confirmar esta actividad para liquidación?', 'question', 'Sí, confirmar')">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                                @endif

                                {{-- Rechazar --}}
                                @if($act->estado_confirmacion === 'pendiente')
                                <form action="{{ route('actividades.confirmar', $act) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <input type="hidden" name="estado_confirmacion" value="rechazado">
                                    <button type="submit"
                                        class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 transition-all flex items-center justify-center shadow-sm"
                                        title="Rechazar Actividad"
                                        onclick="return swConfirm(this, '¿Rechazar esta actividad?', 'warning', 'Sí, rechazar')">
                                        <i class="fas fa-ban text-xs"></i>
                                    </button>
                                </form>
                                @endif

                                {{-- Eliminar --}}
                                @if($act->estado_confirmacion !== 'confirmado')
                                <form action="{{ route('actividades.destroy', $act) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-all flex items-center justify-center shadow-sm"
                                        title="Eliminar"
                                        onclick="return swConfirm(this, '¿Eliminar esta actividad?', 'warning', 'Sí, eliminar')">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                                @endif

                                {{-- Observación --}}
                                @if($act->observacion)
                                <button type="button"
                                    class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-all flex items-center justify-center shadow-sm"
                                    title="{{ $act->observacion }}">
                                    <i class="fas fa-comment-dots text-xs"></i>
                                </button>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center py-12 text-slate-400">
                            <i class="fas fa-clipboard-list text-3xl mb-2 block text-slate-300"></i>
                            No hay actividades registradas en el período seleccionado.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer Summary Bar --}}
    @if($actividades->count() > 0)
    <div class="p-4 bg-emerald-50/50 border-t border-emerald-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs">
        <div class="text-slate-600 font-medium">
            Mostrando registros de labores agrícolas
        </div>
        <div class="flex items-center gap-2">
            <span class="font-bold text-slate-600 uppercase">Subtotal Total Confirmado:</span>
            <span class="font-display font-black text-base text-emerald-700 font-mono">
                ${{ number_format(
                    $actividades->where('estado_confirmacion','confirmado')
                        ->sum(fn($a) => $a->cantidad * ($a->valorActividad->valor_unitario ?? 0) * $a->numero_pasada),
                    0, ',', '.'
                ) }}
            </span>
        </div>
    </div>
    @endif

</div>

@endsection

@section('js')
<script>
$(document).ready(function () {
    if ($('#actividadesTable tbody tr').length > 1 || !$('#actividadesTable tbody tr td[colspan]').length) {
        $('#actividadesTable').DataTable({
            responsive: true,
            order: [[1, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Buscar labor, trabajador o lote...",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ actividades",
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
