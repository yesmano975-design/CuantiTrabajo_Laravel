@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Editar Usuario')

@section('content')

<div class="max-w-3xl mx-auto">
    
    {{-- Breadcrumb & Heading --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="{{ route('usuarios.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1.5 mb-1 transition-colors">
                <i class="fas fa-arrow-left"></i> Volver a usuarios
            </a>
            <h1 class="font-display font-black text-2xl text-slate-800">
                Editar Usuario: {{ $usuario->nombre }} {{ $usuario->apellido }}
            </h1>
            <p class="text-xs text-slate-500">Actualiza los datos de perfil, rol o contraseña</p>
        </div>
    </div>

    {{-- Form Container --}}
    <div class="glass-card overflow-hidden">
        
        <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/90 to-amber-50/30 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-user-pen"></i>
            </div>
            <div>
                <h3 class="font-display font-bold text-base text-slate-800">Modificar Cuenta #{{ $usuario->id }}</h3>
                <span class="text-xs text-slate-500">Actualización de credenciales y permisos</span>
            </div>
        </div>

        <form method="POST" action="{{ route('usuarios.update', $usuario) }}" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

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
                            class="w-full pl-10 pr-4 py-3 rounded-xl border @error('rol_id') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                        <option value="">-- Seleccionar Rol --</option>
                        @foreach($roles as $rol)
                            <option value="{{ $rol->id }}" {{ old('rol_id', $usuario->rol_id) == $rol->id ? 'selected' : '' }}>
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
                        <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre) }}" required
                               class="w-full pl-10 pr-4 py-3 rounded-xl border @error('nombre') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
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
                        <input type="text" name="apellido" value="{{ old('apellido', $usuario->apellido) }}"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Email (Disabled) & Teléfono --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Correo Electrónico
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input type="email" value="{{ $usuario->email }}" disabled
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-slate-100/70 text-slate-500 text-sm font-medium cursor-not-allowed">
                    </div>
                    <span class="text-[11px] text-slate-400">El correo electrónico no puede ser modificado por seguridad.</span>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">
                        Teléfono
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-phone"></i>
                        </div>
                        <input type="text" name="telefono" value="{{ old('telefono', $usuario->telefono) }}" placeholder="Número de contacto"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                    </div>
                </div>
            </div>

            {{-- Contraseñas Nuevas (Opcional) --}}
            <div class="p-5 rounded-2xl bg-amber-500/5 border border-amber-500/20 space-y-4">
                <div class="flex items-center gap-2">
                    <i class="fas fa-key text-amber-600 text-sm"></i>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Cambiar Contraseña (Opcional)</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-600">
                            Nueva Contraseña
                        </label>
                        <input type="password" name="password" placeholder="Dejar en blanco para mantener"
                               class="w-full px-4 py-2.5 rounded-xl border @error('password') border-rose-400 bg-rose-50/30 @else border-slate-200 bg-white @enderror text-slate-800 text-sm focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                        @error('password')
                            <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-slate-600">
                            Confirmar Nueva Contraseña
                        </label>
                        <input type="password" name="password_confirmation" placeholder="Repite la nueva contraseña"
                               class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pt-6 border-t border-slate-100 flex items-center justify-between gap-4">
                <a href="{{ route('usuarios.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-sm transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-xmark"></i> Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fas fa-floppy-disk"></i>
                    <span>Actualizar Usuario</span>
                </button>
            </div>

        </form>
    </div>

</div>

@endsection
