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
        <button type="button" onclick="openModal('modalCrear')" class="btn-primary-custom flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Nueva Actividad</span>
        </button>
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
                                <button type="button"
                                    onclick="openEditActividadModal(
                                        {{ $act->id }},
                                        {{ $act->trabajador_id }},
                                        {{ $act->lote_id }},
                                        {{ $act->valor_actividad_id }},
                                        '{{ $act->fecha->format('Y-m-d') }}',
                                        {{ $act->cantidad }},
                                        {{ $act->numero_pasada }},
                                        '{{ addslashes($act->observacion) }}'
                                    )"
                                   class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition-all flex items-center justify-center shadow-sm"
                                   title="Editar Actividad">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
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

{{-- ══════════════════════════════════════════════════════════════
     MODAL: NUEVA ACTIVIDAD LABORAL
     ══════════════════════════════════════════════════════════════ --}}
<div id="modalCrear" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modalCrear')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[92vh] overflow-y-auto">

        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-emerald-50/40 rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-sm">
                    <i class="fas fa-clipboard-plus"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-lg text-slate-800">Nueva Actividad Laboral</h3>
                    <p class="text-xs text-slate-500">Asigna la labor, trabajador, lote y tarifa vigente</p>
                </div>
            </div>
            <button onclick="closeModal('modalCrear')" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-all flex items-center justify-center">
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('actividades.store') }}" id="formCrearActividad" class="p-6 space-y-5">
            @csrf

            @if($errors->any() && old('_method') === null)
            <div class="px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- Trabajador & Lote --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Trabajador Operario <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-user-check"></i></div>
                        <select name="trabajador_id" required
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                            <option value="">-- Seleccionar Trabajador --</option>
                            @foreach($trabajadores as $t)
                                <option value="{{ $t->id }}" {{ old('trabajador_id')==$t->id?'selected':'' }}>
                                    {{ $t->nombre }} {{ $t->apellido }} ({{ $t->cargo->nombre ?? 'Sin cargo' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Lote / Terreno <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-map-location-dot"></i></div>
                        <select name="lote_id" required
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                            <option value="">-- Seleccionar Lote --</option>
                            @foreach($lotes as $lote)
                                <option value="{{ $lote->id }}" {{ old('lote_id')==$lote->id?'selected':'' }}>
                                    {{ $lote->nombre }} — {{ $lote->referencia }} ({{ number_format($lote->tamano_hectareas,2) }} ha)
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tarifa --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Tipo de Actividad & Tarifa Vigente <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-tags"></i></div>
                    <select name="valor_actividad_id" id="m_tarifaSelect" required
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                        <option value="" data-valor="0" data-unidad="">-- Seleccione la tarifa --</option>
                        @foreach($tarifas as $tarifa)
                            <option value="{{ $tarifa->id }}"
                                data-valor="{{ $tarifa->valor_unitario }}"
                                data-unidad="{{ $tarifa->tipoActividad->unidad_medida ?? '' }}"
                                {{ old('valor_actividad_id')==$tarifa->id?'selected':'' }}>
                                {{ $tarifa->tipoActividad->nombre ?? 'Actividad' }} — ${{ number_format($tarifa->valor_unitario,0,',','.') }} / {{ $tarifa->tipoActividad->unidad_medida ?? 'Unidad' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Fecha, Cantidad, Pasadas --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Fecha Laboral <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-calendar"></i></div>
                        <input type="date" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Cantidad <span id="m_unidadLabel" class="text-emerald-600 lowercase font-normal"></span> <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-calculator"></i></div>
                        <input type="number" name="cantidad" id="m_cantidadInput" value="{{ old('cantidad', 1) }}" min="1" required
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">N° de Pasadas <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-repeat"></i></div>
                        <input type="number" name="numero_pasada" id="m_pasadaInput" value="{{ old('numero_pasada', 1) }}" min="1" required
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Observación --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Observaciones (Opcional)</label>
                <textarea name="observacion" rows="2" placeholder="Notas sobre el clima, máquina utilizada o novedades..."
                          class="w-full p-4 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">{{ old('observacion') }}</textarea>
            </div>

            {{-- Widget Subtotal en vivo --}}
            <div class="p-4 rounded-2xl bg-emerald-500/10 border border-emerald-400/30 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-sm">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <div class="font-display font-bold text-slate-800 text-sm">Subtotal Estimado</div>
                        <div id="m_subtotalFormula" class="text-xs text-slate-500 font-mono mt-0.5">Selecciona tarifa, cantidad y pasadas</div>
                    </div>
                </div>
                <div id="m_subtotalValor" class="font-display font-black text-2xl text-emerald-700 font-mono">$0</div>
            </div>

            {{-- Botones --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('modalCrear')" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-sm transition-all">
                    <i class="fas fa-xmark mr-1"></i> Cancelar
                </button>
                <button type="submit" class="btn-primary-custom flex items-center gap-2">
                    <i class="fas fa-check"></i> Registrar Actividad
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL: EDITAR ACTIVIDAD LABORAL
     ══════════════════════════════════════════════════════════════ --}}
<div id="modalEditar" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modalEditar')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[92vh] overflow-y-auto">

        <div class="flex items-center justify-between p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-amber-50/40 rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-sm">
                    <i class="fas fa-pen-to-square"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-lg text-slate-800">Editar Actividad</h3>
                    <p class="text-xs text-slate-500" id="editActSubtitle">Modificando registro</p>
                </div>
            </div>
            <button onclick="closeModal('modalEditar')" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-all flex items-center justify-center">
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="formEditarActividad" method="POST" action="" class="p-6 space-y-5">
            @csrf @method('PUT')

            @if($errors->any() && old('_method') === 'PUT')
            <div class="px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Trabajador Operario <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-user-check"></i></div>
                        <select name="trabajador_id" id="e_trabajador" required
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                            @foreach($trabajadores as $t)
                                <option value="{{ $t->id }}">{{ $t->nombre }} {{ $t->apellido }} ({{ $t->cargo->nombre ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Lote / Terreno <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-map-location-dot"></i></div>
                        <select name="lote_id" id="e_lote" required
                                class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                            @foreach($lotes as $lote)
                                <option value="{{ $lote->id }}">{{ $lote->nombre }} — {{ $lote->referencia }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Tarifa Vigente <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-tags"></i></div>
                    <select name="valor_actividad_id" id="e_tarifa" required
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                        @foreach($tarifas as $tarifa)
                            <option value="{{ $tarifa->id }}" data-valor="{{ $tarifa->valor_unitario }}" data-unidad="{{ $tarifa->tipoActividad->unidad_medida ?? '' }}">
                                {{ $tarifa->tipoActividad->nombre ?? '' }} — ${{ number_format($tarifa->valor_unitario,0,',','.') }} / {{ $tarifa->tipoActividad->unidad_medida ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Fecha <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-calendar"></i></div>
                        <input type="date" name="fecha" id="e_fecha" max="{{ date('Y-m-d') }}" required
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Cantidad <span id="e_unidadLabel" class="text-amber-600 lowercase font-normal"></span> <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-calculator"></i></div>
                        <input type="number" name="cantidad" id="e_cantidad" min="1" required
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">N° Pasadas <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-repeat"></i></div>
                        <input type="number" name="numero_pasada" id="e_pasada" min="1" required
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                    </div>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Observaciones</label>
                <textarea name="observacion" id="e_observacion" rows="2"
                          class="w-full p-4 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm"></textarea>
            </div>

            <div class="p-4 rounded-2xl bg-amber-500/10 border border-amber-400/30 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-600 text-white flex items-center justify-center shadow-sm"><i class="fas fa-receipt"></i></div>
                    <div>
                        <div class="font-display font-bold text-slate-800 text-sm">Subtotal Recalculado</div>
                        <div id="e_subtotalFormula" class="text-xs text-slate-500 font-mono mt-0.5">Calculando...</div>
                    </div>
                </div>
                <div id="e_subtotalValor" class="font-display font-black text-2xl text-amber-700 font-mono">$0</div>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('modalEditar')" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-sm transition-all">
                    <i class="fas fa-xmark mr-1"></i> Cancelar
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm transition-all shadow-md flex items-center gap-2">
                    <i class="fas fa-floppy-disk"></i> Actualizar Actividad
                </button>
            </div>
        </form>
    </div>
</div>

@section('js')
<script>
// ── Modal helpers ───────────────────────────────────────────────
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if(e.key==='Escape') { closeModal('modalCrear'); closeModal('modalEditar'); } });

// ── Subtotal en vivo ────────────────────────────────────────────
function calcularSubtotalModal() {
    const sel    = document.getElementById('m_tarifaSelect');
    const opt    = sel.options[sel.selectedIndex];
    const valor  = parseFloat(opt?.dataset.valor || 0);
    const unidad = opt?.dataset.unidad || '';
    const cant   = parseInt(document.getElementById('m_cantidadInput').value || 0);
    const pasada = parseInt(document.getElementById('m_pasadaInput').value || 0);

    const lbl = document.getElementById('m_unidadLabel');
    if (lbl) lbl.textContent = unidad ? `(${unidad})` : '';

    const sub = valor * cant * pasada;
    document.getElementById('m_subtotalValor').textContent = '$' + sub.toLocaleString('es-CO');
    document.getElementById('m_subtotalFormula').textContent =
        (valor > 0 && cant > 0 && pasada > 0)
            ? `${cant} × $${valor.toLocaleString('es-CO')} × ${pasada} pasada(s)`
            : 'Selecciona tarifa, cantidad y pasadas';
}

document.getElementById('m_tarifaSelect').addEventListener('change', calcularSubtotalModal);
document.getElementById('m_cantidadInput').addEventListener('input', calcularSubtotalModal);
document.getElementById('m_pasadaInput').addEventListener('input', calcularSubtotalModal);
calcularSubtotalModal();

// ── DataTable ───────────────────────────────────────────────────
$(document).ready(function () {
    if ($('#actividadesTable tbody tr td:not([colspan])').length) {
        $('#actividadesTable').DataTable({
            responsive: true,
            order: [[1, 'desc']],
            language: {
                search: "_INPUT_", searchPlaceholder: "Buscar labor, trabajador o lote...",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ actividades",
                infoEmpty: "Mostrando 0 registros", infoFiltered: "(filtrado de _MAX_)",
                paginate: { first:"«", previous:"‹", next:"›", last:"»" }
            }
        });
    }
});

// ── Subtotal en vivo (modal EDITAR) ────────────────────────────
function calcularSubtotalEditar() {
    const sel    = document.getElementById('e_tarifa');
    const opt    = sel.options[sel.selectedIndex];
    const valor  = parseFloat(opt?.dataset.valor || 0);
    const unidad = opt?.dataset.unidad || '';
    const cant   = parseInt(document.getElementById('e_cantidad').value || 0);
    const pasada = parseInt(document.getElementById('e_pasada').value || 0);

    const lbl = document.getElementById('e_unidadLabel');
    if (lbl) lbl.textContent = unidad ? `(${unidad})` : '';

    const sub = valor * cant * pasada;
    document.getElementById('e_subtotalValor').textContent = '$' + sub.toLocaleString('es-CO');
    document.getElementById('e_subtotalFormula').textContent =
        (valor > 0 && cant > 0 && pasada > 0)
            ? `${cant} × $${valor.toLocaleString('es-CO')} × ${pasada} pasada(s)`
            : 'Selecciona tarifa, cantidad y pasadas';
}

// ── Abrir modal de editar con datos del registro ────────────────
function openEditActividadModal(id, trabajador_id, lote_id, tarifa_id, fecha, cantidad, pasada, observacion) {
    document.getElementById('formEditarActividad').action = '{{ route("actividades.update", ":id") }}'.replace(':id', id);
    document.getElementById('editActSubtitle').textContent = 'Modificando actividad #' + id;
    document.getElementById('e_trabajador').value  = trabajador_id;
    document.getElementById('e_lote').value        = lote_id;
    document.getElementById('e_tarifa').value      = tarifa_id;
    document.getElementById('e_fecha').value       = fecha;
    document.getElementById('e_cantidad').value    = cantidad;
    document.getElementById('e_pasada').value      = pasada;
    document.getElementById('e_observacion').value = observacion;
    calcularSubtotalEditar();
    openModal('modalEditar');
}

document.getElementById('e_tarifa').addEventListener('change', calcularSubtotalEditar);
document.getElementById('e_cantidad').addEventListener('input', calcularSubtotalEditar);
document.getElementById('e_pasada').addEventListener('input', calcularSubtotalEditar);

// ── Reabrir modal si hay errores de validación ──────────────────
@if($errors->any() && old('_method') === null)
    openModal('modalCrear');
@endif
@if($errors->any() && old('_method') === 'PUT')
    openModal('modalEditar');
@endif
</script>
@endsection
