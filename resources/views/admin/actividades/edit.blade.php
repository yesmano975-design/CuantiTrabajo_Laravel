@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Editar Actividad')

@section('content')

<div class="max-w-4xl mx-auto">
    
    {{-- Breadcrumb & Heading --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('actividades.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1.5 mb-1 transition-colors">
                <i class="fas fa-arrow-left"></i> Volver a actividades
            </a>
            <h1 class="font-display font-black text-2xl text-slate-800">
                Editar Actividad #{{ $actividade->id }}
            </h1>
            <p class="text-xs text-slate-500">Fecha de registro: {{ $actividade->fecha->format('d/m/Y') }}</p>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="glass-card overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 to-amber-50/30 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h3 class="font-display font-bold text-base text-slate-800">Modificar Datos de la Labor</h3>
                <span class="text-xs text-slate-500">Recalcula subtotales de forma automática</span>
            </div>
        </div>

        <form method="POST" action="{{ route('actividades.update', $actividade) }}" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            {{-- Fila: Trabajador & Lote --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                
                {{-- Trabajador --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Trabajador Operario <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <select name="trabajador_id" required
                                class="w-full pl-10 pr-4 py-3 rounded-xl border @error('trabajador_id') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                            <option value="">-- Seleccionar Trabajador --</option>
                            @foreach($trabajadores as $t)
                                <option value="{{ $t->id }}" {{ old('trabajador_id', $actividade->trabajador_id) == $t->id ? 'selected' : '' }}>
                                    {{ $t->nombre }} {{ $t->apellido }} ({{ $t->cargo->nombre ?? 'Sin cargo' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('trabajador_id')
                        <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Lote --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Lote / Terreno <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-map-location-dot"></i>
                        </div>
                        <select name="lote_id" required
                                class="w-full pl-10 pr-4 py-3 rounded-xl border @error('lote_id') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                            <option value="">-- Seleccionar Lote --</option>
                            @foreach($lotes as $lote)
                                <option value="{{ $lote->id }}" {{ old('lote_id', $actividade->lote_id) == $lote->id ? 'selected' : '' }}>
                                    {{ $lote->nombre }} — Ref: {{ $lote->referencia }} ({{ number_format($lote->tamano_hectareas, 2) }} ha)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('lote_id')
                        <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            {{-- Tarifa / Tipo de Actividad --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                    Tipo de Actividad & Tarifa <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-tags"></i>
                    </div>
                    <select name="valor_actividad_id" id="tarifaSelect" required
                            class="w-full pl-10 pr-4 py-3 rounded-xl border @error('valor_actividad_id') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                        <option value="" data-valor="0" data-unidad="">-- Seleccione la tarifa --</option>
                        @foreach($tarifas as $tarifa)
                            <option value="{{ $tarifa->id }}"
                                data-valor="{{ $tarifa->valor_unitario }}"
                                data-unidad="{{ $tarifa->tipoActividad->unidad_medida ?? '' }}"
                                {{ old('valor_actividad_id', $actividade->valor_actividad_id) == $tarifa->id ? 'selected' : '' }}>
                                {{ $tarifa->tipoActividad->nombre ?? 'Actividad' }}
                                — ${{ number_format($tarifa->valor_unitario, 0, ',', '.') }} / {{ $tarifa->tipoActividad->unidad_medida ?? 'Unidad' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('valor_actividad_id')
                    <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                        <i class="fas fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Fila: Fecha, Cantidad, N° Pasadas --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                
                {{-- Fecha --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Fecha Laboral <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <input type="date" name="fecha" id="fechaInput"
                               value="{{ old('fecha', $actividade->fecha->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required
                               class="w-full pl-10 pr-4 py-3 rounded-xl border @error('fecha') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                    </div>
                    @error('fecha')
                        <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Cantidad --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Cantidad <span id="unidadLabel" class="text-amber-600 lowercase font-normal"></span> <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <input type="number" name="cantidad" id="cantidadInput"
                               value="{{ old('cantidad', $actividade->cantidad) }}" min="1" required
                               class="w-full pl-10 pr-4 py-3 rounded-xl border @error('cantidad') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                    </div>
                    @error('cantidad')
                        <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- N° Pasadas --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        N° de Pasadas <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-repeat"></i>
                        </div>
                        <input type="number" name="numero_pasada" id="pasadaInput"
                               value="{{ old('numero_pasada', $actividade->numero_pasada) }}" min="1" required
                               class="w-full pl-10 pr-4 py-3 rounded-xl border @error('numero_pasada') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                    </div>
                    @error('numero_pasada')
                        <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

            </div>

            {{-- Observación --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                    Observaciones / Comentarios
                </label>
                <div class="relative">
                    <textarea name="observacion" rows="2" placeholder="Notas sobre el clima, máquina utilizada o novedades..."
                              class="w-full p-4 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">{{ old('observacion', $actividade->observacion) }}</textarea>
                </div>
            </div>

            {{-- Widget Subtotal Preview en Vivo --}}
            <div class="p-5 rounded-2xl bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-transparent border border-amber-400/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-amber-600 text-white flex items-center justify-center text-xl shadow-sm">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <div class="font-display font-bold text-slate-800 text-sm">Subtotal Recalculado</div>
                        <div id="subtotalFormula" class="text-xs text-slate-500 font-mono mt-0.5">Calculando subtotal...</div>
                    </div>
                </div>
                <div class="text-right sm:text-right w-full sm:w-auto">
                    <div id="subtotalValor" class="font-display font-black text-2xl sm:text-3xl text-amber-700 font-mono tracking-tight">$0</div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                <a href="{{ route('actividades.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-sm transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-xmark"></i> Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fas fa-floppy-disk"></i>
                    <span>Actualizar Actividad</span>
                </button>
            </div>

        </form>
    </div>

</div>

@endsection

@section('js')
<script>
function calcularSubtotal() {
    const tarifaSelect = document.getElementById('tarifaSelect');
    const opt    = tarifaSelect.options[tarifaSelect.selectedIndex];
    const valor  = parseFloat(opt?.dataset.valor || 0);
    const unidad = opt?.dataset.unidad || '';
    const cant   = parseInt(document.getElementById('cantidadInput').value || 0);
    const pasada = parseInt(document.getElementById('pasadaInput').value || 0);

    const unidadLabel = document.getElementById('unidadLabel');
    if (unidadLabel) {
        unidadLabel.textContent = unidad ? `(${unidad})` : '';
    }

    const subtotal = valor * cant * pasada;

    document.getElementById('subtotalValor').textContent = '$' + subtotal.toLocaleString('es-CO');

    if (valor > 0 && cant > 0 && pasada > 0) {
        document.getElementById('subtotalFormula').textContent =
            `${cant} × $${valor.toLocaleString('es-CO')} × ${pasada} pasada(s)`;
    } else {
        document.getElementById('subtotalFormula').textContent =
            'Selecciona tarifa, cantidad y pasadas';
    }
}

document.getElementById('tarifaSelect').addEventListener('change', calcularSubtotal);
document.getElementById('cantidadInput').addEventListener('input', calcularSubtotal);
document.getElementById('pasadaInput').addEventListener('input', calcularSubtotal);

calcularSubtotal();
</script>
@endsection
