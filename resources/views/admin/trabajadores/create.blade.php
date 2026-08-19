@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Registrar Trabajador')

@section('content')

<div class="max-w-3xl mx-auto">
    
    {{-- Breadcrumb & Heading --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('trabajadores.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1.5 mb-1 transition-colors">
                <i class="fas fa-arrow-left"></i> Volver a trabajadores
            </a>
            <h1 class="font-display font-black text-2xl text-slate-800">Nuevo Trabajador</h1>
            <p class="text-xs text-slate-500">Registra los datos personales y asigna el cargo operativo en finca</p>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="glass-card overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 to-emerald-50/30 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <h3 class="font-display font-bold text-base text-slate-800">Formulario de Registro</h3>
                <span class="text-xs text-slate-500">Los campos marcados con (*) son obligatorios</span>
            </div>
        </div>

        <form method="POST" action="{{ route('trabajadores.store') }}" class="p-6 sm:p-8 space-y-6">
            @csrf

            {{-- Cargo --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                    Cargo Operativo <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <select name="cargo_id" required
                            class="w-full pl-10 pr-4 py-3 rounded-xl border @error('cargo_id') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                        <option value="">-- Seleccionar Cargo --</option>
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->id }}" {{ old('cargo_id') == $cargo->id ? 'selected' : '' }}>
                                {{ $cargo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('cargo_id')
                    <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                        <i class="fas fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Nombre & Apellido --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Nombres <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-user"></i>
                        </div>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Juan Carlos"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border @error('nombre') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                    @error('nombre')
                        <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Apellidos
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-user-group"></i>
                        </div>
                        <input type="text" name="apellido" value="{{ old('apellido') }}" placeholder="Ej. Pérez Gómez"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Documento --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                    Documento de Identidad <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <input type="text" name="documento" value="{{ old('documento') }}" required placeholder="Cédula de ciudadanía o documento"
                           class="w-full pl-10 pr-4 py-3 rounded-xl border @error('documento') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                </div>
                @error('documento')
                    <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                        <i class="fas fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Correo & Teléfono --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Correo Electrónico (Opcional)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input type="email" name="correo" value="{{ old('correo') }}" placeholder="ejemplo@correo.com"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border @error('correo') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                    @error('correo')
                        <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Teléfono / Celular (Opcional)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-phone"></i>
                        </div>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" placeholder="300 123 4567"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                <a href="{{ route('trabajadores.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-sm transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-xmark"></i> Cancelar
                </a>
                <button type="submit" class="btn-primary-custom flex items-center gap-2">
                    <i class="fas fa-check"></i>
                    <span>Guardar Trabajador</span>
                </button>
            </div>

        </form>
    </div>

</div>

@endsection
