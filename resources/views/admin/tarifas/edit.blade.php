@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Editar Tarifa')

@section('content')

<div class="max-w-2xl mx-auto">
    
    {{-- Breadcrumb & Heading --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('tarifas.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1.5 mb-1 transition-colors">
                <i class="fas fa-arrow-left"></i> Volver a tarifas
            </a>
            <h1 class="font-display font-black text-2xl text-slate-800">
                Editar Tarifa: {{ $tarifa->tipoActividad->nombre ?? '' }}
            </h1>
            <p class="text-xs text-slate-500">Actualiza el valor unitario o las fechas de vigencia</p>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="glass-card overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 to-amber-50/30 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-edit"></i>
            </div>
            <div>
                <h3 class="font-display font-bold text-base text-slate-800">Modificar Tarifa #{{ $tarifa->id }}</h3>
                <span class="text-xs text-slate-500">Asegúrate de comprobar los valores actualizados</span>
            </div>
        </div>

        <form method="POST" action="{{ route('tarifas.update', $tarifa) }}" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

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
                            class="w-full pl-10 pr-4 py-3 rounded-xl border @error('tipo_actividad_id') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                        <option value="">-- Seleccionar Tipo de Actividad --</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}" {{ old('tipo_actividad_id', $tarifa->tipo_actividad_id) == $tipo->id ? 'selected' : '' }}>
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
                    <input type="number" name="valor_unitario" step="0.01" min="0.01" value="{{ old('valor_unitario', $tarifa->valor_unitario) }}" required
                           class="w-full pl-10 pr-4 py-3 rounded-xl border @error('valor_unitario') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
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
                        <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', $tarifa->fecha_inicio->format('Y-m-d')) }}" required
                               class="w-full pl-10 pr-4 py-3 rounded-xl border @error('fecha_inicio') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                    </div>
                    @error('fecha_inicio')
                        <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Fecha Fin de Vigencia
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-calendar-xmark"></i>
                        </div>
                        <input type="date" name="fecha_fin" value="{{ old('fecha_fin', $tarifa->fecha_fin?->format('Y-m-d')) }}"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Estado --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                    Estado de la Tarifa <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-toggle-on"></i>
                    </div>
                    <select name="estado"
                            class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                        <option value="activo"   {{ old('estado', $tarifa->estado) === 'activo'   ? 'selected' : '' }}>Activa (Aplicable a nuevas labores)</option>
                        <option value="inactivo" {{ old('estado', $tarifa->estado) === 'inactivo' ? 'selected' : '' }}>Inactiva (Deshabilitada)</option>
                    </select>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                <a href="{{ route('tarifas.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-sm transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-xmark"></i> Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fas fa-floppy-disk"></i>
                    <span>Actualizar Tarifa</span>
                </button>
            </div>

        </form>
    </div>

</div>

@endsection
