@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Nuevo Tipo de Actividad')

@section('content')

<div class="max-w-2xl mx-auto">
    
    {{-- Breadcrumb & Heading --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('tipo-actividades.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1.5 mb-1 transition-colors">
                <i class="fas fa-arrow-left"></i> Volver a catálogo
            </a>
            <h1 class="font-display font-black text-2xl text-slate-800">Nuevo Tipo de Actividad</h1>
            <p class="text-xs text-slate-500">Crea una categoría de labor y define su unidad de medición</p>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="glass-card overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 to-emerald-50/30 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-tags"></i>
            </div>
            <div>
                <h3 class="font-display font-bold text-base text-slate-800">Definición de Concepto</h3>
                <span class="text-xs text-slate-500">Configura el nombre y formato de cobro</span>
            </div>
        </div>

        <form method="POST" action="{{ route('tipo-actividades.store') }}" class="p-6 sm:p-8 space-y-6">
            @csrf

            {{-- Nombre --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                    Nombre de la Actividad <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-tag"></i>
                    </div>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej: Fumigación / Poda / Abonado"
                           class="w-full pl-10 pr-4 py-3 rounded-xl border @error('nombre') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                </div>
                @error('nombre')
                    <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                        <i class="fas fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Descripción --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                    Descripción (Opcional)
                </label>
                <div class="relative">
                    <textarea name="descripcion" rows="2" placeholder="Detalles de la labor o especificaciones técnicas..."
                              class="w-full p-4 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">{{ old('descripcion') }}</textarea>
                </div>
            </div>

            {{-- Unidad de Medida --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                    Unidad de Medida <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 z-10">
                        <i class="fas fa-ruler-combined"></i>
                    </div>
                    <select name="unidad_medida" required
                            class="w-full pl-10 pr-10 py-3 rounded-xl border @error('unidad_medida') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm appearance-none cursor-pointer">
                        <option value="" disabled {{ old('unidad_medida') ? '' : 'selected' }}>Selecciona una unidad...</option>
                        <option value="horas"    {{ old('unidad_medida') == 'horas'    ? 'selected' : '' }}>⏱ Horas</option>
                        <option value="dias"     {{ old('unidad_medida') == 'dias'     ? 'selected' : '' }}>📅 Días</option>
                        <option value="hectareas"{{ old('unidad_medida') == 'hectareas'? 'selected' : '' }}>🌾 Hectáreas</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
                <span class="text-[11px] text-slate-400">Unidad estándar en la que se cuantifica la ejecución de esta labor.</span>
                @error('unidad_medida')
                    <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                        <i class="fas fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                <a href="{{ route('tipo-actividades.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-sm transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-xmark"></i> Cancelar
                </a>
                <button type="submit" class="btn-primary-custom flex items-center gap-2">
                    <i class="fas fa-check"></i>
                    <span>Guardar Tipo</span>
                </button>
            </div>

        </form>
    </div>

</div>

@endsection
