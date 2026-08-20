@extends('layouts.sidebaradmin')
@section('tituloPagina', 'Panel de Control')

@section('content')

{{-- Banner de bienvenida --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-forest-dark via-forest to-forest-light p-6 sm:p-8 mb-8 shadow-xl text-white border border-white/10">
    <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-emerald-200 border border-white/15 text-xs font-semibold uppercase tracking-wider mb-3">
                <i class="fas fa-leaf"></i> Sistema de Gestión Agrícola
            </div>
            <h1 class="font-display text-2xl sm:text-3xl font-black text-white tracking-tight">
                ¡Bienvenido, {{ Auth::user()->nombre }}! 👋
            </h1>
            <p class="text-white/60 text-sm mt-2 leading-relaxed">
                {{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                @if($actividadesPendientes > 0)
                    — <span class="text-white/90 font-semibold">{{ $actividadesPendientes }} labores pendientes de confirmación.</span>
                @else
                    — Todas las labores están al día y confirmadas.
                @endif
            </p>
            <div class="mt-5 flex flex-wrap items-center gap-3">
                <a href="{{ route('actividades.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white text-forest font-bold text-xs hover:bg-emerald-50 transition-all shadow-sm">
                    <i class="fas fa-plus"></i> Registrar Labor
                </a>
                <a href="{{ route('pagos.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs border border-white/20 transition-all">
                    <i class="fas fa-receipt"></i> Liquidaciones
                </a>
            </div>
        </div>
        <div class="hidden lg:flex items-center gap-4 bg-white/10 p-4 rounded-2xl border border-white/10">
            <div class="w-12 h-12 rounded-xl bg-white/10 text-emerald-300 flex items-center justify-center text-2xl">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="text-right">
                <span class="text-[11px] uppercase tracking-wider text-white/50 font-bold block">Estado Operativo</span>
                <span class="text-sm font-black text-white font-display">100% En Línea</span>
                <span class="text-[10px] text-white/40 block mt-0.5">{{ $totalTrabajadores }} operarios activos</span>
            </div>
        </div>
    </div>
    <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute right-10 top-1/2 -translate-y-1/2 opacity-[0.04] pointer-events-none">
        <i class="fas fa-tractor text-white text-9xl"></i>
    </div>
</div>

{{-- Tarjetas métricas --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

    {{-- Trabajadores --}}
    <a href="{{ route('trabajadores.index') }}"
       class="group glass-card p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between">
        <div>
            <div class="flex items-start justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg shadow-sm group-hover:bg-emerald-700 group-hover:text-white transition-colors duration-200">
                    <i class="fas fa-person-digging"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full uppercase tracking-wider">Activos</span>
            </div>
            <div class="font-display text-3xl font-black text-slate-800 mb-1">{{ $totalTrabajadores }}</div>
            <div class="text-xs font-medium text-slate-500">Trabajadores registrados</div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-emerald-700 font-semibold">
            <span>Gestionar cuadrilla</span>
            <i class="fas fa-arrow-right text-[11px] group-hover:translate-x-1 transition-transform"></i>
        </div>
    </a>

    {{-- Lotes --}}
    <a href="{{ route('lotes.index') }}"
       class="group glass-card p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between">
        <div>
            <div class="flex items-start justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg shadow-sm group-hover:bg-emerald-700 group-hover:text-white transition-colors duration-200">
                    <i class="fas fa-map-location-dot"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full uppercase tracking-wider">Predios</span>
            </div>
            <div class="font-display text-3xl font-black text-slate-800 mb-1">{{ $totalLotes }}</div>
            <div class="text-xs font-medium text-slate-500">Lotes de cultivo</div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-emerald-700 font-semibold">
            <span>Ver mapa de lotes</span>
            <i class="fas fa-arrow-right text-[11px] group-hover:translate-x-1 transition-transform"></i>
        </div>
    </a>

    {{-- Actividades pendientes --}}
    <a href="{{ route('actividades.index') }}"
       class="group glass-card p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between">
        <div>
            <div class="flex items-start justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg shadow-sm group-hover:bg-emerald-700 group-hover:text-white transition-colors duration-200">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>
                @if($actividadesPendientes > 0)
                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200/60 px-2.5 py-1 rounded-full uppercase tracking-wider">Por revisar</span>
                @else
                    <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full uppercase tracking-wider">Al día</span>
                @endif
            </div>
            <div class="font-display text-3xl font-black text-slate-800 mb-1">{{ $actividadesPendientes }}</div>
            <div class="text-xs font-medium text-slate-500">Actividades pendientes</div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-emerald-700 font-semibold">
            <span>Confirmar labores</span>
            <i class="fas fa-arrow-right text-[11px] group-hover:translate-x-1 transition-transform"></i>
        </div>
    </a>

    {{-- Pagos pendientes --}}
    <a href="{{ route('pagos.index') }}"
       class="group glass-card p-6 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 flex flex-col justify-between">
        <div>
            <div class="flex items-start justify-between mb-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg shadow-sm group-hover:bg-emerald-700 group-hover:text-white transition-colors duration-200">
                    <i class="fas fa-money-bill-transfer"></i>
                </div>
                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full uppercase tracking-wider">Nómina</span>
            </div>
            <div class="font-display text-3xl font-black text-slate-800 mb-1">{{ $pagosPendientes }}</div>
            <div class="text-xs font-medium text-slate-500">Semanas por liquidar</div>
        </div>
        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-emerald-700 font-semibold">
            <span>Procesar nómina</span>
            <i class="fas fa-arrow-right text-[11px] group-hover:translate-x-1 transition-transform"></i>
        </div>
    </a>

</div>

{{-- Accesos Rápidos --}}
<div class="glass-card p-6 sm:p-8">
    <div class="mb-6">
        <h3 class="font-display font-bold text-slate-800 text-base flex items-center gap-2">
            <i class="fas fa-bolt text-emerald-600"></i> Accesos Directos
        </h3>
        <p class="text-xs text-slate-400 mt-0.5">Crea nuevos registros con un solo clic</p>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

        <a href="{{ route('actividades.index') }}"
           class="flex flex-col items-center justify-center gap-2.5 p-4 rounded-xl border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/60 transition-all group text-center">
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-sm">
                <i class="fas fa-plus"></i>
            </div>
            <span class="text-[11px] font-semibold text-slate-600 group-hover:text-emerald-800">Nueva Labor</span>
        </a>

        <a href="{{ route('trabajadores.index') }}"
           class="flex flex-col items-center justify-center gap-2.5 p-4 rounded-xl border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/60 transition-all group text-center">
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-sm">
                <i class="fas fa-user-plus"></i>
            </div>
            <span class="text-[11px] font-semibold text-slate-600 group-hover:text-emerald-800">Operario</span>
        </a>

        <a href="{{ route('lotes.index') }}"
           class="flex flex-col items-center justify-center gap-2.5 p-4 rounded-xl border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/60 transition-all group text-center">
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-sm">
                <i class="fas fa-map-location"></i>
            </div>
            <span class="text-[11px] font-semibold text-slate-600 group-hover:text-emerald-800">Nuevo Lote</span>
        </a>

        <a href="{{ route('pagos.index') }}"
           class="flex flex-col items-center justify-center gap-2.5 p-4 rounded-xl border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/60 transition-all group text-center">
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-sm">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <span class="text-[11px] font-semibold text-slate-600 group-hover:text-emerald-800">Liquidar Pagos</span>
        </a>

        @if(Auth::user()->rol->nombre === 'administrador')
        <a href="{{ route('tarifas.index') }}"
           class="flex flex-col items-center justify-center gap-2.5 p-4 rounded-xl border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/60 transition-all group text-center">
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-sm">
                <i class="fas fa-coins"></i>
            </div>
            <span class="text-[11px] font-semibold text-slate-600 group-hover:text-emerald-800">Tarifas</span>
        </a>

        <a href="{{ route('usuarios.index') }}"
           class="flex flex-col items-center justify-center gap-2.5 p-4 rounded-xl border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/60 transition-all group text-center">
            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-sm">
                <i class="fas fa-user-shield"></i>
            </div>
            <span class="text-[11px] font-semibold text-slate-600 group-hover:text-emerald-800">Usuarios</span>
        </a>
        @endif

    </div>
</div>

@endsection
