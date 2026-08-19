@extends('layouts.sidebaradmin')
@section('tituloPagina', 'Panel de Control')

@section('content')

{{-- Banner de bienvenida SaaS --}}
<div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-forest via-slate-900 to-forest-dark p-6 sm:p-8 mb-8 shadow-xl text-white border border-white/10">
    <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-400/20 text-emerald-300 border border-emerald-400/30 text-xs font-semibold uppercase tracking-wider mb-3">
                <i class="fas fa-leaf"></i> Sistema Inteligente de Gestión Agrícola
            </div>
            <h1 class="font-display text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight">
                ¡Bienvenido, {{ Auth::user()->nombre }} {{ Auth::user()->apellido }}! 👋
            </h1>
            <p class="text-slate-300 text-sm mt-2 font-normal leading-relaxed">
                Hoy es <span class="text-emerald-300 font-semibold">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</span>. 
                @if($actividadesPendientes > 0)
                    Tienes <span class="text-amber-300 font-bold underline decoration-amber-400/50 decoration-2 underline-offset-2">{{ $actividadesPendientes }} labores pendientes de confirmación</span> para liquidación de nómina.
                @else
                    Todas las labores de campo se encuentran al día y confirmadas.
                @endif
            </p>
            <div class="mt-5 flex flex-wrap items-center gap-3">
                <a href="{{ route('actividades.create') }}" class="btn-primary-custom flex items-center gap-2 text-xs">
                    <i class="fas fa-plus"></i>
                    <span>Registrar Labor</span>
                </a>
                <a href="{{ route('pagos.index') }}" class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition-all backdrop-blur-md flex items-center gap-2">
                    <i class="fas fa-receipt"></i>
                    <span>Liquidaciones</span>
                </a>
            </div>
        </div>

        {{-- Mini Widget de Estado Rápido --}}
        <div class="hidden lg:flex items-center gap-4 bg-white/10 backdrop-blur-md p-4 rounded-2xl border border-white/10 shadow-inner">
            <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-2xl">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="text-right">
                <span class="text-[11px] uppercase tracking-wider text-slate-300 font-bold block">Estado Operativo</span>
                <span class="text-sm font-black text-emerald-300 font-display">100% En Línea</span>
                <span class="text-[10px] text-slate-400 block mt-0.5">{{ $totalTrabajadores }} operarios activos</span>
            </div>
        </div>
    </div>

    {{-- Decoraciones de Fondo --}}
    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute right-10 top-1/2 -translate-y-1/2 opacity-5 pointer-events-none">
        <i class="fas fa-tractor text-white text-9xl"></i>
    </div>
</div>

{{-- Tarjetas métricas ejecutivas --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

    {{-- Trabajadores --}}
    <a href="{{ route('trabajadores.index') }}"
       class="group glass-card p-6 border-l-4 border-emerald-500 hover:scale-[1.02] transition-all duration-300 flex flex-col justify-between">
        <div>
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                    <i class="fas fa-person-digging"></i>
                </div>
                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-100/80 px-2.5 py-1 rounded-full uppercase tracking-wider">Activos</span>
            </div>
            <div class="font-display text-3xl font-black text-slate-800 mb-1 font-mono">{{ $totalTrabajadores }}</div>
            <div class="text-xs font-semibold text-slate-500">Trabajadores Registrados</div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-emerald-600 font-bold">
            <span>Gestionar cuadrilla</span>
            <i class="fas fa-arrow-right text-[11px] group-hover:translate-x-1 transition-transform"></i>
        </div>
    </a>

    {{-- Lotes --}}
    <a href="{{ route('lotes.index') }}"
       class="group glass-card p-6 border-l-4 border-cyan-500 hover:scale-[1.02] transition-all duration-300 flex flex-col justify-between">
        <div>
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl group-hover:bg-cyan-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                    <i class="fas fa-map-location-dot"></i>
                </div>
                <span class="text-[11px] font-bold text-cyan-700 bg-cyan-100/80 px-2.5 py-1 rounded-full uppercase tracking-wider">Predios</span>
            </div>
            <div class="font-display text-3xl font-black text-slate-800 mb-1 font-mono">{{ $totalLotes }}</div>
            <div class="text-xs font-semibold text-slate-500">Lotes de Cultivo</div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-cyan-600 font-bold">
            <span>Ver mapa de lotes</span>
            <i class="fas fa-arrow-right text-[11px] group-hover:translate-x-1 transition-transform"></i>
        </div>
    </a>

    {{-- Actividades pendientes --}}
    <a href="{{ route('actividades.index') }}"
       class="group glass-card p-6 border-l-4 border-amber-500 hover:scale-[1.02] transition-all duration-300 flex flex-col justify-between">
        <div>
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl group-hover:bg-amber-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>
                @if($actividadesPendientes > 0)
                    <span class="text-[11px] font-bold text-amber-700 bg-amber-100/80 px-2.5 py-1 rounded-full uppercase tracking-wider animate-pulse">Por Aprobar</span>
                @else
                    <span class="text-[11px] font-bold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-full uppercase tracking-wider">Al Día</span>
                @endif
            </div>
            <div class="font-display text-3xl font-black text-slate-800 mb-1 font-mono">{{ $actividadesPendientes }}</div>
            <div class="text-xs font-semibold text-slate-500">Actividades Pendientes</div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-amber-600 font-bold">
            <span>Confirmar labores</span>
            <i class="fas fa-arrow-right text-[11px] group-hover:translate-x-1 transition-transform"></i>
        </div>
    </a>

    {{-- Pagos Pendientes --}}
    <a href="{{ route('pagos.index') }}"
       class="group glass-card p-6 border-l-4 border-rose-500 hover:scale-[1.02] transition-all duration-300 flex flex-col justify-between">
        <div>
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl group-hover:bg-rose-600 group-hover:text-white transition-colors duration-300 shadow-sm">
                    <i class="fas fa-money-bill-transfer"></i>
                </div>
                <span class="text-[11px] font-bold text-rose-700 bg-rose-100/80 px-2.5 py-1 rounded-full uppercase tracking-wider">Nómina</span>
            </div>
            <div class="font-display text-3xl font-black text-slate-800 mb-1 font-mono">{{ $pagosPendientes }}</div>
            <div class="text-xs font-semibold text-slate-500">Semanas por Liquidar</div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-rose-600 font-bold">
            <span>Procesar nómina</span>
            <i class="fas fa-arrow-right text-[11px] group-hover:translate-x-1 transition-transform"></i>
        </div>
    </a>

</div>

{{-- Accesos Rápidos y Visual Features --}}
<div class="glass-card p-6 sm:p-8 mb-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="font-display font-bold text-slate-800 text-lg flex items-center gap-2">
                <i class="fas fa-bolt text-amber-400"></i> Accesos Directos
            </h3>
            <p class="text-xs text-slate-500">Crea nuevos registros con un solo clic</p>
        </div>
    </div>
    
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        {{-- Nueva Actividad --}}
        <a href="{{ route('actividades.create') }}"
           class="flex flex-col items-center justify-center gap-2.5 p-4 rounded-2xl border-2 border-dashed border-emerald-200 hover:border-emerald-500 hover:bg-emerald-50/50 transition-all group text-center">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-lg group-hover:scale-110 transition-transform shadow-sm">
                <i class="fas fa-plus"></i>
            </div>
            <span class="text-xs font-bold text-slate-700 group-hover:text-emerald-800">Nueva Labor</span>
        </a>

        {{-- Nuevo Trabajador --}}
        <a href="{{ route('trabajadores.create') }}"
           class="flex flex-col items-center justify-center gap-2.5 p-4 rounded-2xl border-2 border-dashed border-sky-200 hover:border-sky-500 hover:bg-sky-50/50 transition-all group text-center">
            <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center text-lg group-hover:scale-110 transition-transform shadow-sm">
                <i class="fas fa-user-plus"></i>
            </div>
            <span class="text-xs font-bold text-slate-700 group-hover:text-sky-800">Operario</span>
        </a>

        {{-- Nuevo Lote --}}
        <a href="{{ route('lotes.create') }}"
           class="flex flex-col items-center justify-center gap-2.5 p-4 rounded-2xl border-2 border-dashed border-cyan-200 hover:border-cyan-500 hover:bg-cyan-50/50 transition-all group text-center">
            <div class="w-12 h-12 rounded-xl bg-cyan-100 text-cyan-700 flex items-center justify-center text-lg group-hover:scale-110 transition-transform shadow-sm">
                <i class="fas fa-map-location"></i>
            </div>
            <span class="text-xs font-bold text-slate-700 group-hover:text-cyan-800">Nuevo Lote</span>
        </a>

        {{-- Liquidar Pagos --}}
        <a href="{{ route('pagos.index') }}"
           class="flex flex-col items-center justify-center gap-2.5 p-4 rounded-2xl border-2 border-dashed border-amber-200 hover:border-amber-500 hover:bg-amber-50/50 transition-all group text-center">
            <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center text-lg group-hover:scale-110 transition-transform shadow-sm">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <span class="text-xs font-bold text-slate-700 group-hover:text-amber-800">Liquidar Pagos</span>
        </a>

        {{-- Nueva Tarifa --}}
        <a href="{{ route('tarifas.create') }}"
           class="flex flex-col items-center justify-center gap-2.5 p-4 rounded-2xl border-2 border-dashed border-teal-200 hover:border-teal-500 hover:bg-teal-50/50 transition-all group text-center">
            <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center text-lg group-hover:scale-110 transition-transform shadow-sm">
                <i class="fas fa-coins"></i>
            </div>
            <span class="text-xs font-bold text-slate-700 group-hover:text-teal-800">Nueva Tarifa</span>
        </a>

        {{-- Nuevo Usuario --}}
        <a href="{{ route('usuarios.create') }}"
           class="flex flex-col items-center justify-center gap-2.5 p-4 rounded-2xl border-2 border-dashed border-purple-200 hover:border-purple-500 hover:bg-purple-50/50 transition-all group text-center">
            <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center text-lg group-hover:scale-110 transition-transform shadow-sm">
                <i class="fas fa-user-shield"></i>
            </div>
            <span class="text-xs font-bold text-slate-700 group-hover:text-purple-800">Nuevo Usuario</span>
        </a>
    </div>
</div>

@endsection
