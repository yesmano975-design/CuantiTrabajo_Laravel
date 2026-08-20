@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Tarifas')

@section('content')

{{-- Tabla Principal --}}
<div class="glass-card overflow-hidden">
    {{-- Card Header --}}
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gradient-to-r from-slate-50/80 to-emerald-50/30">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-forest text-emerald-300 flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div>
                <h2 class="font-display font-bold text-xl text-slate-800">Tarifas por Actividad</h2>
                <p class="text-xs text-slate-500">Valores unitarios por jornal, hectárea o unidad de labor agrícola</p>
            </div>
        </div>
        <button type="button" onclick="openModal('modalCrear')" class="btn-primary-custom flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Nueva Tarifa</span>
        </button>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
    <div class="mx-6 mt-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center gap-2">
        <i class="fas fa-circle-check"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mx-6 mt-4 px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm flex items-center gap-2">
        <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
    </div>
    @endif

    {{-- Table Area --}}
    <div class="p-6">
        <div class="overflow-x-auto">
            <table id="tarifasTable" class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tipo de Actividad</th>
                        <th>Unidad de Medida</th>
                        <th class="text-right">Valor Unitario</th>
                        <th>Vigencia Desde</th>
                        <th>Vigencia Hasta</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tarifas as $tarifa)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="font-bold text-slate-400 text-xs">#{{ $tarifa->id }}</td>
                        <td>
                            <div class="font-bold text-slate-800">{{ $tarifa->tipoActividad->nombre ?? '-' }}</div>
                            <div class="text-xs text-slate-400">{{ $tarifa->tipoActividad->descripcion ?: 'Sin descripción adicional' }}</div>
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                <i class="fas fa-scale-unbalanced text-[10px] text-slate-400"></i>
                                {{ $tarifa->tipoActividad->unidad_medida ?? '-' }}
                            </span>
                        </td>
                        <td class="text-right">
                            <span class="font-display font-black text-emerald-700 text-base font-mono">
                                ${{ number_format($tarifa->valor_unitario, 2) }}
                            </span>
                        </td>
                        <td class="font-medium text-slate-600">
                            {{ $tarifa->fecha_inicio->format('d/m/Y') }}
                        </td>
                        <td class="font-medium text-slate-600">
                            {{ $tarifa->fecha_fin ? $tarifa->fecha_fin->format('d/m/Y') : 'Indefinida' }}
                        </td>
                        <td>
                            @if($tarifa->estado === 'activo')
                                <span class="badge-active">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activa
                                </span>
                            @else
                                <span class="badge-inactive">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Inactiva
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Editar --}}
                                <button type="button"
                                    onclick="openEditModal({{ $tarifa->id }}, {{ $tarifa->tipo_actividad_id }}, {{ $tarifa->valor_unitario }}, '{{ $tarifa->fecha_inicio->format('Y-m-d') }}', '{{ $tarifa->fecha_fin?->format('Y-m-d') }}', '{{ $tarifa->estado }}')"
                                    class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                    title="Editar Tarifa">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                </button>

                                {{-- Eliminar --}}
                                <form action="{{ route('tarifas.destroy', $tarifa) }}"
                                      method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                        title="Eliminar Tarifa"
                                        onclick="return swConfirm(this, '¿Eliminar esta tarifa?', 'warning', 'Sí, eliminar')">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-10 text-slate-400">
                            <i class="fas fa-dollar-sign text-3xl mb-2 block text-slate-300"></i>
                            No hay tarifas configuradas en el sistema.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL CREAR --}}
{{-- ============================================================ --}}
<div id="modalCrear" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modalCrear')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-lg text-slate-800">Nueva Tarifa</h3>
                    <p class="text-xs text-slate-500">Registrar un valor unitario para un tipo de actividad</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalCrear')"
                class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors flex items-center justify-center">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        {{-- Form --}}
        <form id="formCrear" action="{{ route('tarifas.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            {{-- Errores --}}
            @if($errors->any() && old('_method') === null)
            <div class="px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Tipo de Actividad --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Tipo de Actividad <span class="text-rose-500">*</span>
                    </label>
                    <select name="tipo_actividad_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-sm text-slate-800 bg-white">
                        <option value="" disabled {{ old('tipo_actividad_id') ? '' : 'selected' }}>Seleccionar actividad...</option>
                        @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id }}" {{ old('tipo_actividad_id') == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre }} (Medido en: {{ $tipo->unidad_medida }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Valor Unitario --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Valor Unitario <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="valor_unitario" value="{{ old('valor_unitario') }}"
                        required step="0.01" min="0.01"
                        placeholder="0.00"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-sm font-mono placeholder-slate-400">
                </div>

                {{-- Fecha Inicio --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Fecha Inicio <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio', date('Y-m-d')) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-sm text-slate-800">
                </div>

                {{-- Fecha Fin --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Fecha Fin <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                    </label>
                    <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-sm text-slate-800">
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalCrear')"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition text-sm font-medium">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-floppy-disk"></i> Guardar Tarifa
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL EDITAR --}}
{{-- ============================================================ --}}
<div id="modalEditar" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modalEditar')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    <i class="fas fa-pen-to-square"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-lg text-slate-800">Editar Tarifa</h3>
                    <p class="text-xs text-slate-500">Modificar el valor unitario y vigencia</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalEditar')"
                class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors flex items-center justify-center">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        {{-- Form --}}
        <form id="formEditar" action="" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            {{-- Errores edición --}}
            @if($errors->any() && old('_method') === 'PUT')
            <div class="px-4 py-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Tipo de Actividad --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Tipo de Actividad <span class="text-rose-500">*</span>
                    </label>
                    <select id="edit_tipo" name="tipo_actividad_id" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm text-slate-800 bg-white">
                        @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id }}">
                            {{ $tipo->nombre }} (Medido en: {{ $tipo->unidad_medida }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Valor Unitario --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Valor Unitario <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="edit_valor" name="valor_unitario"
                        required step="0.01" min="0.01"
                        placeholder="0.00"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm font-mono placeholder-slate-400">
                </div>

                {{-- Estado --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Estado <span class="text-rose-500">*</span>
                    </label>
                    <select id="edit_estado" name="estado" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm text-slate-800 bg-white">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>

                {{-- Fecha Inicio --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Fecha Inicio <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" id="edit_fecha_inicio" name="fecha_inicio" required
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm text-slate-800">
                </div>

                {{-- Fecha Fin --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Fecha Fin <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                    </label>
                    <input type="date" id="edit_fecha_fin" name="fecha_fin"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm text-slate-800">
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalEditar')"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition text-sm font-medium">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-floppy-disk"></i> Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<script>
// ── Modal helpers ──────────────────────────────────────────────
function openModal(id) {
    const el = document.getElementById(id);
    el.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeModal(id) {
    const el = document.getElementById(id);
    el.classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeModal('modalCrear');
        closeModal('modalEditar');
    }
});

// ── Open Edit Modal ────────────────────────────────────────────
function openEditModal(id, tipo_id, valor, fecha_inicio, fecha_fin, estado) {
    document.getElementById('formEditar').action = '/tarifas/' + id;
    document.getElementById('edit_tipo').value         = tipo_id;
    document.getElementById('edit_valor').value        = valor;
    document.getElementById('edit_fecha_inicio').value = fecha_inicio;
    document.getElementById('edit_fecha_fin').value    = fecha_fin || '';
    document.getElementById('edit_estado').value       = estado;
    openModal('modalEditar');
}

// ── DataTable ──────────────────────────────────────────────────
$(document).ready(function () {
    if ($('#tarifasTable tbody tr').length > 1 || !$('#tarifasTable tbody tr td[colspan]').length) {
        $('#tarifasTable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Buscar tarifa...",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ tarifas",
                infoEmpty: "Mostrando 0 registros",
                infoFiltered: "(filtrado de _MAX_ totales)",
                paginate: { first: "«", previous: "‹", next: "›", last: "»" }
            }
        });
    }
});

// ── Reabrir modal crear si hay errores de validación ───────────
@if($errors->any() && old('_method') === null)
    openModal('modalCrear');
@endif
</script>
@endsection
