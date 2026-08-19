@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Gestión de Pagos')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">

    {{-- ===== COLUMNA IZQUIERDA: Semanas disponibles ===== --}}
    <div class="lg:col-span-4 space-y-4">
        <div class="glass-card overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 to-amber-50/30 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center text-base shadow-sm">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-base text-slate-800">Semanas de Labores</h3>
                    <p class="text-[11px] text-slate-500">Con actividades confirmadas para liquidar</p>
                </div>
            </div>

            <div class="p-4 space-y-3 max-h-[550px] overflow-y-auto">
                @forelse($semanas as $semana)
                @php
                    $esActiva = $semana->lunes === $lunesActual && $semana->sabado === $sabadoActual;
                    $pagada   = $historial->where('periodo_inicio', $semana->lunes)->where('periodo_fin', $semana->sabado)->first();
                @endphp
                <a href="{{ route('pagos.index', ['lunes' => $semana->lunes, 'sabado' => $semana->sabado]) }}"
                   class="block p-4 rounded-2xl border transition-all duration-200 {{ $esActiva ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-500/20 shadow-md' : 'border-slate-200 bg-white hover:border-emerald-300 hover:shadow-sm' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="font-display font-bold text-sm text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-calendar-day text-emerald-600 text-xs"></i>
                                {{ \Carbon\Carbon::parse($semana->lunes)->format('d M') }} — {{ \Carbon\Carbon::parse($semana->sabado)->format('d M, Y') }}
                            </div>
                            <div class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                                <span><i class="fas fa-clipboard-check text-[10px] text-slate-400"></i> {{ $semana->num_actividades }} labores</span>
                                <span>•</span>
                                <span><i class="fas fa-user-group text-[10px] text-slate-400"></i> {{ $semana->num_trabajadores }} operarios</span>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($pagada)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $pagada->estado === 'pagado' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    <i class="fas fa-circle-check text-[8px]"></i>
                                    {{ $pagada->estado === 'pagado' ? 'Pagado' : 'Generado' }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                    Sin liquidar
                                </span>
                            @endif
                            <div class="font-display font-black text-sm text-emerald-700 font-mono mt-1">
                                ${{ number_format($semana->total_semana ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="text-center text-slate-400 py-10">
                    <i class="fas fa-calendar-xmark text-3xl mb-2 block text-slate-300"></i>
                    No hay semanas con actividades confirmadas.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ===== COLUMNA DERECHA: Resumen de Semana Seleccionada ===== --}}
    <div class="lg:col-span-8">
        @if($lunesActual && $sabadoActual)
        <div class="glass-card overflow-hidden">
            
            {{-- Header Liquidación --}}
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 to-emerald-50/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-forest text-emerald-300 flex items-center justify-center text-lg shadow-sm">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-lg text-slate-800">
                            Semana: {{ \Carbon\Carbon::parse($lunesActual)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($sabadoActual)->format('d/m/Y') }}
                        </h3>
                        <p class="text-xs text-slate-500">Detalle consolidado por trabajador para generación de nómina</p>
                    </div>
                </div>

                @if(!$yaGenerado && $resumenSemana->count() > 0)
                @php $totalFormateado = number_format($totalSemana, 0, ',', '.'); @endphp
                <form action="{{ route('pagos.store') }}" method="POST" class="m-0">
                    @csrf
                    <input type="hidden" name="lunes"  value="{{ $lunesActual }}">
                    <input type="hidden" name="sabado" value="{{ $sabadoActual }}">
                    <button type="submit" class="btn-primary-custom flex items-center gap-2"
                        onclick="return swConfirm(this, '¿Generar liquidación por ${{ $totalFormateado }}?', 'question', 'Sí, generar')">
                        <i class="fas fa-hand-holding-dollar"></i>
                        <span>Generar Liquidación</span>
                    </button>
                </form>
                @elseif($yaGenerado)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                    <i class="fas fa-circle-check text-emerald-500"></i>
                    Liquidación ya generada
                </span>
                @endif
            </div>

            <div class="p-6">
                @if($resumenSemana->count() > 0)

                {{-- Banner Total a Pagar --}}
                <div class="mb-6 p-6 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-700 text-white text-center shadow-lg shadow-emerald-700/10">
                    <span class="text-xs uppercase font-bold tracking-widest text-emerald-100">Total a Liquidar en la Semana</span>
                    <div class="font-display font-black text-4xl sm:text-5xl font-mono tracking-tight mt-1 mb-1">
                        ${{ number_format($totalSemana, 0, ',', '.') }}
                    </div>
                    <span class="text-xs text-emerald-200">{{ $resumenSemana->count() }} trabajadores con actividades aprobadas</span>
                </div>

                {{-- Listado por Trabajador --}}
                <div class="space-y-4">
                    @foreach($resumenSemana as $item)
                    <div class="rounded-2xl border border-slate-200 overflow-hidden bg-white shadow-sm">
                        {{-- Worker Header --}}
                        <div class="p-4 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center">
                                    {{ substr($item['trabajador']->nombre, 0, 1) }}{{ substr($item['trabajador']->apellido ?? '', 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 text-sm">
                                        {{ $item['trabajador']->nombre }} {{ $item['trabajador']->apellido }}
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        Doc: {{ $item['trabajador']->documento }} • {{ $item['trabajador']->cargo->nombre ?? 'Sin cargo' }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-display font-black text-base text-emerald-700 font-mono">
                                    ${{ number_format($item['total'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Worker Tasks Table --}}
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs text-left">
                                <thead class="bg-slate-100/60 text-slate-600 font-semibold border-b border-slate-100">
                                    <tr>
                                        <th class="py-2.5 px-3">Fecha</th>
                                        <th class="py-2.5 px-3">Actividad</th>
                                        <th class="py-2.5 px-3">Lote</th>
                                        <th class="py-2.5 px-3 text-center">Cant.</th>
                                        <th class="py-2.5 px-3 text-center">Pasadas</th>
                                        <th class="py-2.5 px-3 text-right">V. Unitario</th>
                                        <th class="py-2.5 px-3 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-700">
                                    @foreach($item['actividades'] as $act)
                                    @php
                                        $vu  = $act->valorActividad->valor_unitario ?? 0;
                                        $sub = $act->cantidad * $vu * $act->numero_pasada;
                                    @endphp
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="py-2 px-3 font-medium whitespace-nowrap">{{ $act->fecha->format('d/m') }}</td>
                                        <td class="py-2 px-3">{{ $act->valorActividad->tipoActividad->nombre ?? '-' }}</td>
                                        <td class="py-2 px-3 text-slate-500">{{ $act->lote->nombre ?? '-' }}</td>
                                        <td class="py-2 px-3 text-center font-bold">{{ $act->cantidad }}</td>
                                        <td class="py-2 px-3 text-center font-bold">{{ $act->numero_pasada }}</td>
                                        <td class="py-2 px-3 text-right font-mono">${{ number_format($vu, 0, ',', '.') }}</td>
                                        <td class="py-2 px-3 text-right font-mono font-bold text-emerald-700">
                                            ${{ number_format($sub, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>

                @else
                <div class="text-center text-slate-400 py-16">
                    <i class="fas fa-circle-check text-4xl mb-3 block text-emerald-400"></i>
                    <h4 class="text-slate-700 font-bold text-base">No hay actividades confirmadas en esta semana</h4>
                    <p class="text-xs text-slate-400 mt-1">Aprueba actividades en el módulo de actividades para que aparezcan en el resumen.</p>
                </div>
                @endif
            </div>

        </div>
        @else
        <div class="glass-card p-12 text-center text-slate-400">
            <i class="fas fa-hand-pointer text-4xl mb-3 block text-slate-300"></i>
            <h4 class="font-display font-bold text-slate-700 text-lg">Selecciona una semana</h4>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Haz clic en una de las semanas de la lista izquierda para visualizar el detalle y generar el pago.</p>
        </div>
        @endif
    </div>

</div>

{{-- ===== HISTORIAL DE PAGOS GENERADOS ===== --}}
<div class="glass-card overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-50/80 to-slate-100/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-800 text-white flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-clock-rotate-left"></i>
            </div>
            <div>
                <h2 class="font-display font-bold text-xl text-slate-800">Historial de Pagos Generados</h2>
                <p class="text-xs text-slate-500">Registro histórico de nóminas liquidadas y recibos de pago</p>
            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="overflow-x-auto">
            <table id="historialTable" class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr>
                        <th># ID</th>
                        <th>Fecha de Emisión</th>
                        <th>Período Liquidado</th>
                        <th class="text-center">Actividades</th>
                        <th class="text-right">Total Liquidado</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($historial as $pago)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="font-bold text-slate-400 text-xs">#{{ $pago->id }}</td>
                        <td class="font-medium text-slate-700">
                            {{ $pago->fecha_generacion->format('d/m/Y') }}
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1.5 font-semibold text-slate-700 text-xs">
                                <i class="fas fa-calendar-week text-slate-400"></i>
                                {{ \Carbon\Carbon::parse($pago->periodo_inicio)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($pago->periodo_fin)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $pago->detalle_pagos_count }} ítems
                            </span>
                        </td>
                        <td class="text-right font-mono font-black text-emerald-700 text-base">
                            ${{ number_format($pago->total_pago, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($pago->estado === 'pagado')
                                <span class="badge-active">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Pagado
                                </span>
                            @else
                                <span class="badge-pending">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Generado
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Ver Detalle --}}
                                <a href="{{ route('pagos.show', $pago) }}"
                                   class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                   title="Ver Recibo / Imprimir">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>

                                {{-- Marcar como pagado --}}
                                @if($pago->estado !== 'pagado')
                                <form action="{{ route('pagos.marcarPagado', $pago) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                        title="Marcar como Pagado"
                                        onclick="return swConfirm(this, '¿Marcar el pago #{{ $pago->id }} como pagado?', 'question', 'Sí, marcar pagado')">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                                @endif

                                {{-- Eliminar --}}
                                @if($pago->estado !== 'pagado')
                                <form action="{{ route('pagos.destroy', $pago) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                        title="Eliminar Pago"
                                        onclick="return swConfirm(this, '¿Eliminar la liquidación #{{ $pago->id }}?', 'warning', 'Sí, eliminar')">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-slate-400">
                            <i class="fas fa-receipt text-3xl mb-2 block text-slate-300"></i>
                            No hay historial de pagos registrados todavía.
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
    if ($('#historialTable tbody tr').length > 1 || !$('#historialTable tbody tr td[colspan]').length) {
        $('#historialTable').DataTable({
            responsive: true,
            order: [[0, 'desc']],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Buscar en historial...",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ pagos",
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
