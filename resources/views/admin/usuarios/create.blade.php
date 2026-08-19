@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Registrar Usuario')

@section('content')

<div class="max-w-3xl mx-auto">
    
    {{-- Breadcrumb & Heading --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('usuarios.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1.5 mb-1 transition-colors">
                <i class="fas fa-arrow-left"></i> Volver a usuarios
            </a>
            <h1 class="font-display font-black text-2xl text-slate-800">Nuevo Usuario del Sistema</h1>
            <p class="text-xs text-slate-500">Crea credenciales de acceso con asignación de roles y permisos</p>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="glass-card overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 to-emerald-50/30 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <h3 class="font-display font-bold text-base text-slate-800">Crear Cuenta</h3>
                <span class="text-xs text-slate-500">Define los privilegios y claves de autenticación</span>
            </div>
        </div>

        <form method="POST" action="{{ route('usuarios.store') }}" class="p-6 sm:p-8 space-y-6">
            @csrf

            {{-- Rol --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                    Rol del Usuario <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <select name="rol_id" required
                            class="w-full pl-10 pr-4 py-3 rounded-xl border @error('rol_id') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                        <option value="">-- Seleccionar Rol --</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}" {{ old('rol_id') == $rol->id ? 'selected' : '' }}>
                                {{ ucfirst($rol->nombre) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('rol_id')
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
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Nombre"
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
                        <input type="text" name="apellido" value="{{ old('apellido') }}" placeholder="Apellido"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Email & Teléfono --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Correo Electrónico <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="usuario@correo.com"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border @error('email') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                    @error('email')
                        <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Teléfono
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-phone"></i>
                        </div>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" placeholder="Número de contacto"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Contraseñas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Contraseña <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input type="password" name="password" required placeholder="Mínimo 6 caracteres"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border @error('password') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                    @error('password')
                        <p class="text-xs text-rose-500 font-semibold flex items-center gap-1 mt-1">
                            <i class="fas fa-circle-exclamation"></i> {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Confirmar Contraseña <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-lock-open"></i>
                        </div>
                        <input type="password" name="password_confirmation" required placeholder="Repite la contraseña"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                <a href="{{ route('usuarios.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-sm transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-xmark"></i> Cancelar
                </a>
                <button type="submit" class="btn-primary-custom flex items-center gap-2">
                    <i class="fas fa-check"></i>
                    <span>Guardar Usuario</span>
                </button>
            </div>

        </form>
    </div>

</div>

@endsection
