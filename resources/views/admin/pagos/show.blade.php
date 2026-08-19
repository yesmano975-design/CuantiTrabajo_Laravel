@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Liquidación de Pago #' . $pago->id)

@section('css')
<style>
    @media print {
        header, aside, .no-print { display: none !important; }
        body { background: #fff !important; }
        .main-content { padding: 0 !important; margin: 0 !important; }
        .printable-invoice { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
    }
</style>
@endsection

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    {{-- Acciones y Navegación --}}
    <div class="no-print flex items-center justify-between">
        <a href="{{ route('pagos.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-1.5 transition-colors">
            <i class="fas fa-arrow-left"></i> Volver al módulo de pagos
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold transition-all shadow-sm flex items-center gap-2">
                <i class="fas fa-print"></i>
                <span>Imprimir Recibo / Factura</span>
            </button>
        </div>
    </div>

    {{-- Invoice Container --}}
    <div class="printable-invoice glass-card overflow-hidden bg-white">
        
        {{-- Header Invoice --}}
        <div class="p-8 border-b border-slate-100 bg-gradient-to-r from-forest to-slate-900 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-400/20 text-emerald-300 border border-emerald-400/30 uppercase tracking-wider">
                        Comprobante de Liquidación
                    </span>
                    @if($pago->estado === 'pagado')
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500 text-white">
                            <i class="fas fa-check-circle mr-1"></i> PAGADO
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500 text-slate-950">
                            <i class="fas fa-clock mr-1"></i> GENERADO
                        </span>
                    @endif
                </div>
                <h1 class="font-display font-black text-3xl text-white">Liquidación #{{ str_pad($pago->id, 5, '0', STR_PAD_LEFT) }}</h1>
                <p class="text-xs text-slate-300">Generado el {{ $pago->fecha_generacion->format('d/m/Y h:i A') }}</p>
            </div>

            <div class="text-left sm:text-right bg-white/10 backdrop-blur-md px-5 py-3 rounded-2xl border border-white/10">
                <span class="text-[11px] uppercase font-bold text-slate-300 tracking-wider">Total Liquidado</span>
                <div class="font-display font-black text-2xl sm:text-3xl text-emerald-300 font-mono">
                    ${{ number_format($pago->total_pago, 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- Meta Information Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 p-6 sm:p-8 border-b border-slate-100 bg-slate-50/50">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Período de Labor</span>
                <div class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fas fa-calendar-range text-emerald-600"></i>
                    {{ $pago->periodo_inicio->format('d/m/Y') }} — {{ $pago->periodo_fin->format('d/m/Y') }}
                </div>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Actividades</span>
                <div class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fas fa-clipboard-check text-blue-600"></i>
                    {{ $pago->detallePagos->count() }} labores registradas
                </div>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Operarios</span>
                <div class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fas fa-users text-amber-600"></i>
                    {{ count($porTrabajador) }} trabajadores liquidados
                </div>
            </div>
        </div>

        {{-- Worker Breakdown Blocks --}}
        <div class="p-6 sm:p-8 space-y-6">
            <h3 class="font-display font-bold text-base text-slate-800 flex items-center gap-2">
                <i class="fas fa-layer-group text-emerald-600"></i> Desglose Detallado por Operario
            </h3>

            @foreach($porTrabajador as $item)
            <div class="rounded-2xl border border-slate-200 overflow-hidden">
                {{-- Worker Subheader --}}
                <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center">
                            {{ substr($item['trabajador']->nombre, 0, 1) }}{{ substr($item['trabajador']->apellido ?? '', 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 text-sm">
                                {{ $item['trabajador']->nombre }} {{ $item['trabajador']->apellido }}
                            </div>
                            <div class="text-xs text-slate-500">
                                C.C. {{ $item['trabajador']->documento }} • Cargo: {{ $item['trabajador']->cargo->nombre ?? 'Sin cargo' }}
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-slate-400 block">Subtotal:</span>
                        <span class="font-display font-black text-base text-emerald-700 font-mono">
                            ${{ number_format($item['subtotal'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-white text-slate-600 border-b border-slate-100">
                            <tr>
                                <th class="py-2.5 px-4">Fecha</th>
                                <th class="py-2.5 px-4">Labor</th>
                                <th class="py-2.5 px-4">Lote</th>
                                <th class="py-2.5 px-4 text-center">Cant.</th>
                                <th class="py-2.5 px-4 text-center">Pasadas</th>
                                <th class="py-2.5 px-4 text-right">V. Unitario</th>
                                <th class="py-2.5 px-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            @foreach($item['detalles'] as $detalle)
                            @php $act = $detalle->actividadLaboral; @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="py-2.5 px-4 whitespace-nowrap font-medium text-slate-600">{{ $act->fecha->format('d/m/Y') }}</td>
                                <td class="py-2.5 px-4 font-semibold text-slate-800">{{ $act->valorActividad->tipoActividad->nombre ?? '-' }}</td>
                                <td class="py-2.5 px-4 text-slate-500">{{ $act->lote->nombre ?? '-' }} ({{ $act->lote->referencia ?? '' }})</td>
                                <td class="py-2.5 px-4 text-center font-bold">{{ $detalle->cantidad }}</td>
                                <td class="py-2.5 px-4 text-center font-bold">{{ $act->numero_pasada }}</td>
                                <td class="py-2.5 px-4 text-right font-mono">${{ number_format($detalle->valor_unitario, 0, ',', '.') }}</td>
                                <td class="py-2.5 px-4 text-right font-mono font-bold text-emerald-700">
                                    ${{ number_format($detalle->subtotal, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-slate-50 font-bold border-t border-slate-200">
                            <tr>
                                <td colspan="6" class="py-2.5 px-4 text-right text-slate-600 uppercase text-[11px]">Total Trabajador:</td>
                                <td class="py-2.5 px-4 text-right text-emerald-700 font-mono text-sm">
                                    ${{ number_format($item['subtotal'], 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endforeach

            {{-- Resumen Final Invoice --}}
            <div class="mt-8 p-6 rounded-2xl bg-slate-900 text-white flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <h4 class="font-display font-black text-lg text-white">CuantiTrabajo - Sistema de Gestión Agrícola</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Liquidación oficial generada para soporte contable y de tesorería</p>
                </div>
                <div class="text-right">
                    <span class="text-xs uppercase font-bold tracking-wider text-emerald-400 block">Total Liquidado Definitivo</span>
                    <span class="font-display font-black text-3xl text-white font-mono tracking-tight">
                        ${{ number_format($pago->total_pago, 0, ',', '.') }}
                    </span>
                </div>
            </div>

        </div>

    </div>

</div>

@endsection
