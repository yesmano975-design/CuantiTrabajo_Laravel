@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Nueva Tarifa')

@section('content')

<div class="max-w-2xl mx-auto">
    
    {{-- Breadcrumb & Heading --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('tarifas.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1.5 mb-1 transition-colors">
                <i class="fas fa-arrow-left"></i> Volver a tarifas
            </a>
            <h1 class="font-display font-black text-2xl text-slate-800">Nueva Tarifa de Labor</h1>
            <p class="text-xs text-slate-500">Establece el precio a liquidar por cada unidad de actividad agrícola</p>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="glass-card overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 to-emerald-50/30 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div>
                <h3 class="font-display font-bold text-base text-slate-800">Configuración de Tarifa</h3>
                <span class="text-xs text-slate-500">Define el tipo de labor y la vigencia temporal</span>
            </div>
        </div>

        <form method="POST" action="{{ route('tarifas.store') }}" class="p-6 sm:p-8 space-y-6">
            @csrf

            {{-- Tipo de Actividad --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                    Tipo de Actividad <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-tags"></i>
                    </div>
                    <select name="tipo_actividad_id" required
                            class="w-full pl-10 pr-4 py-3 rounded-xl border @error('tipo_actividad_id') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                        <option value="">-- Seleccionar Tipo de Actividad --</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}" {{ old('tipo_actividad_id', request('tipo')) == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre }} (Medido en: {{ $tipo->unidad_medida }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('tipo_actividad_id')
                    <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                        <i class="fas fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Valor Unitario --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                    Valor Unitario ($) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-coins"></i>
                    </div>
                    <input type="number" name="valor_unitario" step="0.01" min="0.01" value="{{ old('valor_unitario') }}" required placeholder="Ej: 25000.00"
                           class="w-full pl-10 pr-4 py-3 rounded-xl border @error('valor_unitario') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                </div>
                @error('valor_unitario')
                    <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                        <i class="fas fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Fechas Inicio / Fin --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Fecha Inicio de Vigencia <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', date('Y-m-d')) }}" required
                               class="w-full pl-10 pr-4 py-3 rounded-xl border @error('fecha_inicio') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                    @error('fecha_inicio')
                        <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Fecha Fin de Vigencia (Opcional)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-calendar-xmark"></i>
                        </div>
                        <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                    <span class="text-[11px] text-slate-400">Dejar en blanco si es una tarifa activa sin caducidad.</span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                <a href="{{ route('tarifas.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-sm transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-xmark"></i> Cancelar
                </a>
                <button type="submit" class="btn-primary-custom flex items-center gap-2">
                    <i class="fas fa-check"></i>
                    <span>Guardar Tarifa</span>
                </button>
            </div>

        </form>
    </div>

</div>

@endsection
