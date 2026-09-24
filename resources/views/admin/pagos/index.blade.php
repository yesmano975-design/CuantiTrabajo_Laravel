@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Gestión de Pagos')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">

    {{-- ===== COLUMNA IZQUIERDA ===== --}}
    <div class="lg:col-span-4 space-y-4">

        {{-- Semanas de Labores --}}
        <div class="glass-card overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 to-amber-50/30 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-harvest-500 text-white flex items-center justify-center text-base shadow-sm">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-base text-slate-800">Semanas de Labores</h3>
                    <p class="text-[11px] text-slate-500">Con actividades confirmadas para liquidar</p>
                </div>
            </div>

            <div class="p-4 space-y-3 max-h-[320px] overflow-y-auto">
                @forelse($semanas as $semana)
                @php
                    $esActiva  = $semana->lunes === $lunesActual && $semana->sabado === $sabadoActual;
                    $lunesStr  = \Carbon\Carbon::parse($semana->lunes)->format('Y-m-d');
                    $sabadoStr = \Carbon\Carbon::parse($semana->sabado)->format('Y-m-d');
                    $pagada    = $historial->first(fn($p) =>
                        \Carbon\Carbon::parse($p->periodo_inicio)->format('Y-m-d') === $lunesStr &&
                        \Carbon\Carbon::parse($p->periodo_fin)->format('Y-m-d')    === $sabadoStr
                    );
                @endphp
                <a href="{{ route('pagos.index', ['lunes' => $semana->lunes, 'sabado' => $semana->sabado]) }}"
                   class="block p-4 rounded-2xl border transition-all duration-200 {{ $esActiva ? 'border-brand-500 bg-brand-50/40 ring-2 ring-brand-500/20 shadow-md' : 'border-slate-200 bg-white hover:border-brand-300 hover:shadow-sm' }}">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="font-display font-bold text-sm text-slate-800 flex items-center gap-1.5">
                                <i class="fas fa-calendar-day text-brand-600 text-xs"></i>
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
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $pagada->estado === 'pagado' ? 'bg-brand-100 text-brand-800' : 'bg-harvest-100 text-harvest-800' }}">
                                    <i class="fas fa-circle-check text-[8px]"></i>
                                    {{ $pagada->estado === 'pagado' ? 'Pagado' : 'Generado' }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                    Sin liquidar
                                </span>
                            @endif
                            <div class="font-display font-black text-sm text-brand-700 font-mono mt-1">
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

    {{-- ===== COLUMNA DERECHA: Banner resumen de semana ===== --}}
    <div class="lg:col-span-8">
        @if($lunesActual && $sabadoActual)
        <div class="glass-card overflow-hidden">

            {{-- Header --}}
            <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 to-brand-50/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-forest text-brand-300 flex items-center justify-center text-lg shadow-sm">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-lg text-slate-800">
                            Semana: {{ \Carbon\Carbon::parse($lunesActual)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($sabadoActual)->format('d/m/Y') }}
                        </h3>
                        <p class="text-xs text-slate-500">Detalle consolidado por trabajador para generación de nómina</p>
                    </div>
                </div>
                @if($yaGenerado)
                @php
                    $totalPendientes = $actividadesPendientes->sum(fn($g) => $g['actividades']->count());
                @endphp
                @if($totalPendientes > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-harvest-50 text-harvest-700 border border-harvest-300 shadow-sm">
                    <i class="fas fa-triangle-exclamation text-harvest-500"></i>
                    {{ $totalPendientes }} liquidación(es) pendiente(s)
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200 shadow-sm">
                    <i class="fas fa-circle-check text-brand-500"></i>
                    Liquidación ya generada
                </span>
                @endif
                @endif
            </div>

            <div class="p-6">
                @if($resumenSemana->count() > 0)



                {{-- Lista de operarios clickeables --}}
                @if($resumenSemana->count() > 0)
                <div class="rounded-2xl border border-slate-200 overflow-hidden bg-white shadow-sm">
                    <div class="px-4 py-3 border-b border-slate-100 bg-slate-50/60 flex items-center gap-2">
                        <i class="fas fa-users text-brand-600 text-sm"></i>
                        <span class="font-display font-bold text-sm text-slate-700">Operarios esta semana</span>
                    </div>
                    <div class="p-3 space-y-2">
                        @foreach($resumenSemana as $idx => $item)
                        <button type="button"
                            onclick="abrirModalTrabajador({{ $idx }})"
                            class="w-full text-left p-3.5 rounded-xl border border-slate-200 bg-white hover:border-brand-400 hover:bg-brand-50/30 hover:shadow-sm transition-all group flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 font-bold text-sm flex items-center justify-center flex-shrink-0 group-hover:bg-brand-200 transition-colors">
                                {{ substr($item['trabajador']->nombre, 0, 1) }}{{ substr($item['trabajador']->apellido ?? '', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-slate-800 text-sm truncate">
                                    {{ $item['trabajador']->nombre }} {{ $item['trabajador']->apellido }}
                                </div>
                                <div class="text-[11px] text-slate-400 flex items-center gap-1.5 mt-0.5">
                                    <span>{{ $item['trabajador']->cargo->nombre ?? 'Sin cargo' }}</span>
                                    <span>·</span>
                                    <span>{{ $item['num_actividades'] }} labor(es)</span>
                                    @if(!empty($item['actividades_pendientes']) && $item['actividades_pendientes']->count() > 0)
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-harvest-100 text-harvest-700 border border-harvest-300">
                                        <i class="fas fa-circle-exclamation text-[8px]"></i>
                                        {{ $item['actividades_pendientes']->count() }} pend.
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="font-display font-black text-sm text-brand-700 font-mono">
                                    ${{ number_format($item['total'] + ($item['total_pendiente'] ?? 0), 0, ',', '.') }}
                                </div>
                                <i class="fas fa-chevron-right text-[10px] text-slate-300 group-hover:text-brand-400 transition-colors mt-0.5 block"></i>
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endif



                @else
                <div class="text-center text-slate-400 py-16">
                    <i class="fas fa-circle-check text-4xl mb-3 block text-brand-300"></i>
                    <h4 class="text-slate-700 font-bold text-base">No hay actividades confirmadas en esta semana</h4>
                    <p class="text-xs text-slate-400 mt-1">Aprueba actividades en el módulo de actividades para que aparezcan aquí.</p>
                </div>
                @endif
            </div>

        </div>
        @else
        <div class="glass-card p-12 text-center text-slate-400">
            <i class="fas fa-hand-pointer text-4xl mb-3 block text-slate-300"></i>
            <h4 class="font-display font-bold text-slate-700 text-lg">Selecciona una semana</h4>
            <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Haz clic en una semana del panel izquierdo para ver el resumen.</p>
        </div>
        @endif
    </div>

</div>

{{-- ===== HISTORIAL DE PAGOS GENERADOS ===== --}}
<div class="glass-card overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-slate-50/80 to-slate-100/50">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-forest text-white flex items-center justify-center text-lg shadow-sm">
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
                        <td class="font-medium text-slate-700">{{ $pago->fecha_generacion->format('d/m/Y') }}</td>
                        <td>
                            <span class="inline-flex items-center gap-1.5 font-semibold text-slate-700 text-xs">
                                <i class="fas fa-calendar-week text-slate-400"></i>
                                {{ \Carbon\Carbon::parse($pago->periodo_inicio)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($pago->periodo_fin)->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-700 border border-brand-200">
                                {{ $pago->detalle_pagos_count }} ítems
                            </span>
                        </td>
                        <td class="text-right font-mono font-black text-brand-700 text-base">
                            ${{ number_format($pago->total_pago, 0, ',', '.') }}
                        </td>
                        <td>
                            @if($pago->estado === 'pagado')
                                <span class="badge-active"><span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span> Pagado</span>
                            @else
                                <span class="badge-pending"><span class="w-1.5 h-1.5 rounded-full bg-harvest-500"></span> Generado</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" onclick="abrirModalPago({{ $pago->id }})"
                                   class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 hover:bg-brand-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                   title="Ver Recibo">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                                @if($pago->estado !== 'pagado')
                                <form action="{{ route('pagos.marcarPagado', $pago) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 hover:bg-brand-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                        title="Marcar como Pagado">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
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

{{-- ===== MODALES POR TRABAJADOR (semana activa) ===== --}}
@if($lunesActual && $sabadoActual)
@foreach($resumenSemana as $idx => $item)
@php
    $trabajador = $item['trabajador'];
    $actividades = $item['actividades'];
    $totalItem = $item['total'];
@endphp
<div id="modalTrabajador{{ $idx }}"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     onclick="if(event.target===this) cerrarModalTrabajador({{ $idx }})">

    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col" style="max-height:90vh;">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-3.5 bg-white border-b border-slate-100 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-brand-100 text-brand-700 font-bold text-sm flex items-center justify-center">
                    {{ substr($trabajador->nombre, 0, 1) }}{{ substr($trabajador->apellido ?? '', 0, 1) }}
                </div>
                <div>
                    <div class="font-bold text-slate-800 text-sm">{{ $trabajador->nombre }} {{ $trabajador->apellido }}</div>
                    <div class="text-[11px] text-slate-400">Doc: {{ $trabajador->documento }} · {{ $trabajador->cargo->nombre ?? 'Sin cargo' }}</div>
                </div>
            </div>
            <button onclick="cerrarModalTrabajador({{ $idx }})"
                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-rose-100 hover:text-rose-600 text-slate-500 flex items-center justify-center transition-all">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        {{-- Contenido --}}
        <div class="overflow-y-auto flex-1">

            {{-- Semana y total --}}
            <div class="p-5 bg-gradient-to-r from-forest to-forest-light text-white flex items-center justify-between gap-4">
                <div>
                    <div class="text-[11px] uppercase font-bold text-brand-300 tracking-wider mb-0.5">Período de labor</div>
                    <div class="font-bold text-white text-sm flex items-center gap-2">
                        <i class="fas fa-calendar-range text-brand-300"></i>
                        {{ \Carbon\Carbon::parse($lunesActual)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($sabadoActual)->format('d/m/Y') }}
                    </div>
                    <div class="text-[11px] text-brand-300 mt-1">{{ $item['num_actividades'] }} actividad(es) confirmada(s)</div>
                </div>
                <div class="text-right bg-white/10 px-4 py-2.5 rounded-xl border border-white/10 flex-shrink-0">
                    <div class="text-[11px] uppercase font-bold text-brand-300 tracking-wider">A liquidar</div>
                    <div class="font-display font-black text-2xl text-white font-mono">${{ number_format($totalItem, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Tabla de actividades --}}
            <div class="p-5">
                @if($item['actividades']->count() > 0)
                <h4 class="font-display font-bold text-sm text-slate-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-brand-600"></i>
                    {{ ($item['liquidado'] ?? false) ? 'Actividades Liquidadas' : 'Detalle de Actividades' }}
                </h4>
                <div class="rounded-xl border border-slate-200 overflow-hidden mb-4">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-100">
                            <tr>
                                <th class="py-2.5 px-3">Fecha</th>
                                <th class="py-2.5 px-3">Actividad</th>
                                <th class="py-2.5 px-3">Lote</th>
                                <th class="py-2.5 px-3 text-center">Cant.</th>
                                <th class="py-2.5 px-3 text-center">Pas.</th>
                                <th class="py-2.5 px-3 text-right">V. Unit.</th>
                                <th class="py-2.5 px-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($actividades as $act)
                            @php
                                $actObj = ($act instanceof \App\Models\ActividadLaboral) ? $act : null;
                                $vu  = $actObj ? ($actObj->valorActividad->valor_unitario ?? 0) : 0;
                                $sub = $actObj ? ($actObj->cantidad * $vu * $actObj->numero_pasada) : 0;
                            @endphp
                            <tr class="hover:bg-brand-50/30">
                                <td class="py-2 px-3 font-medium whitespace-nowrap">{{ \Carbon\Carbon::parse($actObj->fecha)->format('d/m/Y') }}</td>
                                <td class="py-2 px-3 font-semibold">{{ $actObj->valorActividad->tipoActividad->nombre ?? '-' }}</td>
                                <td class="py-2 px-3 text-slate-500">{{ $actObj->lote->nombre ?? '-' }}</td>
                                <td class="py-2 px-3 text-center font-bold">{{ $actObj->cantidad }}</td>
                                <td class="py-2 px-3 text-center font-bold">{{ $actObj->numero_pasada }}</td>
                                <td class="py-2 px-3 text-right font-mono">${{ number_format($vu, 0, ',', '.') }}</td>
                                <td class="py-2 px-3 text-right font-mono font-bold text-brand-700">${{ number_format($sub, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 border-t border-slate-200">
                            <tr>
                                <td colspan="6" class="py-2.5 px-3 text-right text-slate-500 font-bold text-[11px] uppercase">Total liquidado:</td>
                                <td class="py-2.5 px-3 text-right font-mono font-black text-brand-700 text-sm">${{ number_format($totalItem, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif

                {{-- Actividades pendientes de incluir (solo si el trabajador tiene alguna) --}}
                @if(!empty($item['actividades_pendientes']) && $item['actividades_pendientes']->count() > 0)
                <div class="p-4 rounded-xl border-2 border-harvest-400/50 bg-harvest-50/40">
                    <div class="flex items-center gap-2 mb-3">
                        <i class="fas fa-triangle-exclamation text-harvest-600"></i>
                        <span class="font-bold text-sm text-harvest-800">
                            {{ $item['actividades_pendientes']->count() }} actividad(es) pendiente(s) de incluir
                        </span>
                    </div>
                    <form action="{{ route('pagos.agregarActividades', $pagoExistente) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="space-y-2">
                            @foreach($item['actividades_pendientes'] as $act)
                            @php $vu = $act->valorActividad->valor_unitario ?? 0; $sub = $act->cantidad * $vu * $act->numero_pasada; @endphp
                            <label class="flex items-center gap-3 p-2.5 rounded-lg bg-white border border-slate-200 cursor-pointer hover:border-brand-300 hover:bg-brand-50/30 transition-all">
                                <input type="checkbox" name="actividad_ids[]" value="{{ $act->id }}"
                                    class="w-4 h-4 rounded text-brand-600 border-slate-300 focus:ring-brand-500" checked>
                                <div class="flex-1 text-xs">
                                    <span class="font-semibold text-slate-700">{{ $act->fecha->format('d/m') }}</span>
                                    <span class="text-slate-500 ml-1">· {{ $act->valorActividad->tipoActividad->nombre ?? '-' }}</span>
                                    <span class="text-slate-400 ml-1">· {{ $act->lote->nombre ?? '-' }}</span>
                                    <span class="text-slate-500 ml-1">· {{ $act->cantidad }} × ${{ number_format($vu, 0, ',', '.') }}</span>
                                </div>
                                <span class="font-mono font-bold text-xs text-brand-700">${{ number_format($sub, 0, ',', '.') }}</span>
                            </label>
                            @endforeach
                        </div>
                        <button type="submit"
                            class="mt-3 w-full py-2.5 rounded-lg bg-harvest-500 hover:bg-harvest-600 text-white text-xs font-bold transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-plus-circle"></i> Agregar al pago #{{ $pagoExistente->id }}
                        </button>
                    </form>
                </div>
                @endif

                {{-- Si no tiene actividades liquidadas ni pendientes --}}
                @if($item['actividades']->count() === 0 && (empty($item['actividades_pendientes']) || $item['actividades_pendientes']->count() === 0))
                <div class="text-center py-8 text-slate-400">
                    <i class="fas fa-clipboard-xmark text-2xl mb-2 block"></i>
                    <p class="text-sm">No hay actividades registradas para este operario.</p>
                </div>
                @endif
            </div>

            {{-- Botón liquidar (solo si la semana no está liquidada) --}}
            @if(!$yaGenerado)
            <div class="px-5 pb-5">
                <div class="p-4 rounded-xl bg-brand-50/60 border border-brand-200/60 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="text-sm text-slate-600">
                        Genera la liquidación de <strong>toda la semana</strong> incluyendo todos los operarios.
                    </div>
                    <form action="{{ route('pagos.store') }}" method="POST" class="m-0 flex-shrink-0">
                        @csrf
                        <input type="hidden" name="lunes"  value="{{ $lunesActual }}">
                        <input type="hidden" name="sabado" value="{{ $sabadoActual }}">
                        <div class="relative inline-flex">
                            <span class="absolute inset-0 rounded-xl bg-brand-400 opacity-30 animate-ping"></span>
                            <button type="submit"
                                class="relative inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-sm text-white shadow-lg shadow-brand-600/30
                                       bg-gradient-to-r from-brand-600 to-forest hover:from-brand-500 hover:to-brand-700
                                       ring-2 ring-brand-400/50 transition-all duration-200 hover:scale-105 active:scale-95">
                                <i class="fas fa-hand-holding-dollar"></i>
                                <span>Generar Liquidación Semana</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @else
            <div class="px-5 pb-5">
                <div class="p-3 rounded-xl bg-brand-50 border border-brand-200 flex items-center gap-2 text-brand-700 text-xs font-bold">
                    <i class="fas fa-circle-check text-brand-500"></i>
                    Esta semana ya fue liquidada. Total registrado: ${{ number_format($totalSemana, 0, ',', '.') }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endforeach
@endif

{{-- ===== MODALES DE RECIBO DEL HISTORIAL ===== --}}
@foreach($historial as $pago)
@php
    $porTrabajador = $pago->detallePagos
        ->groupBy(fn($d) => $d->actividadLaboral->trabajador_id)
        ->map(fn($detalles) => [
            'trabajador' => $detalles->first()->actividadLaboral->trabajador,
            'detalles'   => $detalles,
            'subtotal'   => $detalles->sum('subtotal'),
        ])->values();
@endphp
<div id="modalPago{{ $pago->id }}"
     class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     onclick="if(event.target===this) cerrarModal({{ $pago->id }})">

    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

    <div class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col" style="max-height:90vh;">

        <div class="flex items-center justify-between px-5 py-3 bg-white border-b border-slate-100 flex-shrink-0">
            <div class="flex items-center gap-2 text-sm font-bold text-slate-700">
                <i class="fas fa-file-invoice-dollar text-brand-600"></i>
                Recibo de Liquidación #{{ str_pad($pago->id, 5, '0', STR_PAD_LEFT) }}
            </div>
            <button onclick="cerrarModal({{ $pago->id }})"
                class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-rose-100 hover:text-rose-600 text-slate-500 flex items-center justify-center transition-all">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <div class="overflow-y-auto flex-1">
            <div class="p-6 bg-gradient-to-r from-forest to-forest-light text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-400/20 text-brand-200 border border-brand-400/30 uppercase tracking-wider">Comprobante</span>
                        @if($pago->estado === 'pagado')
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-500 text-white"><i class="fas fa-check-circle mr-1"></i> PAGADO</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-harvest-500 text-slate-950"><i class="fas fa-clock mr-1"></i> GENERADO</span>
                        @endif
                    </div>
                    <h2 class="font-display font-black text-2xl text-white">Liquidación #{{ str_pad($pago->id, 5, '0', STR_PAD_LEFT) }}</h2>
                    <p class="text-xs text-brand-200">{{ $pago->fecha_generacion->format('d/m/Y') }}</p>
                </div>
                <div class="text-left sm:text-right bg-white/10 px-4 py-2.5 rounded-xl border border-white/10">
                    <span class="text-[11px] uppercase font-bold text-brand-200 tracking-wider block">Total</span>
                    <div class="font-display font-black text-2xl text-white font-mono">${{ number_format($pago->total_pago, 0, ',', '.') }}</div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 px-6 py-4 border-b border-slate-100 bg-slate-50/60">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Período</span>
                    <div class="font-bold text-slate-700 text-xs flex items-center gap-1.5">
                        <i class="fas fa-calendar-range text-brand-600"></i>
                        {{ $pago->periodo_inicio->format('d/m/Y') }} — {{ $pago->periodo_fin->format('d/m/Y') }}
                    </div>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Actividades</span>
                    <div class="font-bold text-slate-700 text-xs flex items-center gap-1.5">
                        <i class="fas fa-clipboard-check text-brand-600"></i>
                        {{ $pago->detallePagos->count() }} labores
                    </div>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-0.5">Operarios</span>
                    <div class="font-bold text-slate-700 text-xs flex items-center gap-1.5">
                        <i class="fas fa-users text-harvest-600"></i>
                        {{ count($porTrabajador) }} trabajadores
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-5">
                @foreach($porTrabajador as $item)
                <div class="rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                    <div class="p-3.5 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                {{ substr($item['trabajador']->nombre, 0, 1) }}{{ substr($item['trabajador']->apellido ?? '', 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 text-sm">{{ $item['trabajador']->nombre }} {{ $item['trabajador']->apellido }}</div>
                                <div class="text-[11px] text-slate-400">C.C. {{ $item['trabajador']->documento }} · {{ $item['trabajador']->cargo->nombre ?? 'Sin cargo' }}</div>
                            </div>
                        </div>
                        <span class="font-display font-black text-sm text-brand-700 font-mono">${{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left">
                            <thead class="bg-white text-slate-500 font-semibold border-b border-slate-100">
                                <tr>
                                    <th class="py-2 px-3">Fecha</th>
                                    <th class="py-2 px-3">Labor</th>
                                    <th class="py-2 px-3">Lote</th>
                                    <th class="py-2 px-3 text-center">Cant.</th>
                                    <th class="py-2 px-3 text-center">Pas.</th>
                                    <th class="py-2 px-3 text-right">V. Unit.</th>
                                    <th class="py-2 px-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach($item['detalles'] as $detalle)
                                @php $act = $detalle->actividadLaboral; @endphp
                                <tr class="hover:bg-brand-50/30">
                                    <td class="py-2 px-3 whitespace-nowrap font-medium">{{ $act->fecha->format('d/m/Y') }}</td>
                                    <td class="py-2 px-3 font-semibold">{{ $act->valorActividad->tipoActividad->nombre ?? '-' }}</td>
                                    <td class="py-2 px-3 text-slate-500">{{ $act->lote->nombre ?? '-' }}</td>
                                    <td class="py-2 px-3 text-center font-bold">{{ $detalle->cantidad }}</td>
                                    <td class="py-2 px-3 text-center font-bold">{{ $act->numero_pasada }}</td>
                                    <td class="py-2 px-3 text-right font-mono">${{ number_format($detalle->valor_unitario, 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 text-right font-mono font-bold text-brand-700">${{ number_format($detalle->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-slate-50 border-t border-slate-200 font-bold">
                                <tr>
                                    <td colspan="6" class="py-2 px-3 text-right text-slate-500 text-[11px] uppercase">Total:</td>
                                    <td class="py-2 px-3 text-right text-brand-700 font-mono">${{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endforeach

                <div class="p-4 rounded-xl bg-forest text-white flex items-center justify-between">
                    <div>
                        <div class="font-display font-bold text-sm text-brand-200">CuantiTrabajo — Gestión Agrícola</div>
                        <div class="text-[11px] text-brand-300/70">Liquidación oficial para soporte contable</div>
                    </div>
                    <div class="text-right">
                        <span class="text-[11px] uppercase font-bold tracking-wider text-brand-300 block">Total Definitivo</span>
                        <span class="font-display font-black text-2xl text-white font-mono">${{ number_format($pago->total_pago, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

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
                paginate: { first: "«", previous: "‹", next: "›", last: "»" }
            }
        });
    }
});

function abrirModalTrabajador(idx) {
    const modal = document.getElementById('modalTrabajador' + idx);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function cerrarModalTrabajador(idx) {
    const modal = document.getElementById('modalTrabajador' + idx);
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

function abrirModalPago(id) {
    const modal = document.getElementById('modalPago' + id);
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function cerrarModal(id) {
    const modal = document.getElementById('modalPago' + id);
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id^="modalTrabajador"], [id^="modalPago"]').forEach(m => {
            m.classList.add('hidden');
            m.classList.remove('flex');
        });
        document.body.style.overflow = '';
    }
});
</script>
@endsection
