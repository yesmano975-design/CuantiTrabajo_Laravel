@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Lotes de Cultivo')

@section('content')

{{-- Resumen de Métricas --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
    {{-- Total Lotes --}}
    <div class="glass-card p-5 border-l-4 border-cyan-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Lotes Registrados</span>
            <div class="text-3xl font-display font-black text-slate-800 mt-1">{{ $lotes->count() }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-map-location-dot"></i>
        </div>
    </div>

    {{-- Total Hectáreas --}}
    <div class="glass-card p-5 border-l-4 border-emerald-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Área Total Administrada</span>
            <div class="text-3xl font-display font-black text-emerald-600 mt-1">{{ number_format($totalHectareas, 2) }} <span class="text-base font-normal text-slate-500">ha</span></div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-seedling"></i>
        </div>
    </div>
</div>

{{-- Tabla Principal --}}
<div class="glass-card overflow-hidden">
    {{-- Card Header --}}
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gradient-to-r from-slate-50/80 to-cyan-50/30">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-forest text-cyan-300 flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-map-location-dot"></i>
            </div>
            <div>
                <h2 class="font-display font-bold text-xl text-slate-800">Listado de Lotes</h2>
                <p class="text-xs text-slate-500">Control de parcelas, tamaño en hectáreas y actividades vinculadas</p>
            </div>
        </div>
        <button type="button" onclick="openModal('modalCrear')" class="btn-primary-custom flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Nuevo Lote</span>
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
            <table id="lotesTable" class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lote / Nombre</th>
                        <th>Referencia</th>
                        <th>Ubicación</th>
                        <th>Extensión (ha)</th>
                        <th>Actividades Registradas</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($lotes as $lote)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="font-bold text-slate-400 text-xs">#{{ $lote->id }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-cyan-50 border border-cyan-200 text-cyan-700 flex items-center justify-center text-sm font-bold shadow-inner">
                                    <i class="fas fa-map-pin"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">{{ $lote->nombre }}</div>
                                    <div class="text-xs text-slate-400">{{ $lote->ubicacion ?: 'Sin coordenadas especificadas' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="font-mono bg-slate-100 px-2 py-1 rounded text-xs text-slate-600 border border-slate-200 font-semibold">
                                {{ $lote->referencia }}
                            </span>
                        </td>
                        <td class="text-slate-600 font-medium">
                            {{ $lote->ubicacion ?: '-' }}
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="fas fa-vector-square text-[10px]"></i>
                                {{ number_format($lote->tamano_hectareas, 2) }} ha
                            </span>
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                <i class="fas fa-clipboard-list text-[10px]"></i>
                                {{ $lote->actividadesLaborales()->count() }} labores
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Editar --}}
                                <button type="button"
                                    onclick="openEditModal({{ $lote->id }}, '{{ addslashes($lote->nombre) }}', '{{ addslashes($lote->referencia) }}', '{{ addslashes($lote->ubicacion) }}', {{ $lote->tamano_hectareas }})"
                                    class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                    title="Editar Lote">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                </button>

                                {{-- Eliminar --}}
                                <form action="{{ route('lotes.destroy', $lote) }}"
                                      method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                        title="Eliminar Lote"
                                        onclick="return swConfirm(this, '¿Eliminar este lote?', 'warning', 'Sí, eliminar')"
                                        {{ $lote->actividadesLaborales()->count() > 0 ? 'disabled title=No_se_puede_eliminar_tiene_actividades' : '' }}>
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-slate-400">
                            <i class="fas fa-map-location-dot text-3xl mb-2 block text-slate-300"></i>
                            No hay lotes registrados actualmente.
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
                <div class="w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-lg">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-lg text-slate-800">Nuevo Lote</h3>
                    <p class="text-xs text-slate-500">Registrar una nueva parcela de cultivo</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalCrear')"
                class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors flex items-center justify-center">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        {{-- Form --}}
        <form id="formCrear" action="{{ route('lotes.store') }}" method="POST" class="p-6 space-y-5">
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
                {{-- Nombre --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Nombre del Lote <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required
                        placeholder="Ej: Lote Norte"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 outline-none transition text-sm text-slate-800 placeholder-slate-400">
                </div>

                {{-- Referencia --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Referencia <span class="text-rose-500">*</span>
                        <span class="text-slate-400 font-normal normal-case text-[10px] ml-1">(auto-generada)</span>
                    </label>
                    <input type="text" id="crear_referencia" name="referencia" value="{{ old('referencia') }}" required
                        placeholder="LT-001"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 outline-none transition text-sm font-mono uppercase placeholder-slate-400 bg-slate-50"
                        oninput="this.value = this.value.toUpperCase()">
                    <p class="text-[11px] text-slate-400 mt-1">Se genera automáticamente al escribir el nombre. Puedes editarla.</p>
                </div>

                {{-- Tamaño ha --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Extensión (hectáreas) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" name="tamano_hectareas" value="{{ old('tamano_hectareas') }}"
                        required step="0.01" min="0.01"
                        placeholder="0.00"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 outline-none transition text-sm font-mono placeholder-slate-400">
                </div>

                {{-- Ubicación --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Ubicación / Coordenadas <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                    </label>
                    <input type="text" name="ubicacion" value="{{ old('ubicacion') }}"
                        placeholder="Ej: Vereda El Palmar, coordenadas GPS..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 outline-none transition text-sm text-slate-800 placeholder-slate-400">
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalCrear')"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition text-sm font-medium">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-600 text-white font-bold text-sm transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-floppy-disk"></i> Guardar Lote
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
                    <h3 class="font-display font-bold text-lg text-slate-800">Editar Lote</h3>
                    <p class="text-xs text-slate-500">Modificar los datos de la parcela</p>
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
                {{-- Nombre --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Nombre del Lote <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="edit_nombre" name="nombre" required
                        placeholder="Ej: Lote Norte"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm text-slate-800 placeholder-slate-400">
                </div>

                {{-- Referencia --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Referencia <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="edit_referencia" name="referencia" required
                        placeholder="Ej: LT-001"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm font-mono uppercase placeholder-slate-400"
                        oninput="this.value = this.value.toUpperCase()">
                </div>

                {{-- Tamaño ha --}}
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Extensión (hectáreas) <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" id="edit_tamano" name="tamano_hectareas"
                        required step="0.01" min="0.01"
                        placeholder="0.00"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm font-mono placeholder-slate-400">
                </div>

                {{-- Ubicación --}}
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                        Ubicación / Coordenadas <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                    </label>
                    <input type="text" id="edit_ubicacion" name="ubicacion"
                        placeholder="Ej: Vereda El Palmar, coordenadas GPS..."
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm text-slate-800 placeholder-slate-400">
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
function openEditModal(id, nombre, referencia, ubicacion, tamano) {
    document.getElementById('formEditar').action = '/lotes/' + id;
    document.getElementById('edit_nombre').value    = nombre;
    document.getElementById('edit_referencia').value = referencia;
    document.getElementById('edit_ubicacion').value  = ubicacion;
    document.getElementById('edit_tamano').value     = tamano;
    openModal('modalEditar');
}

// ── DataTable ──────────────────────────────────────────────────
$(document).ready(function () {
    if ($('#lotesTable tbody tr').length > 1 || !$('#lotesTable tbody tr td[colspan]').length) {
        $('#lotesTable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Buscar lote...",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ lotes",
                infoEmpty: "Mostrando 0 registros",
                infoFiltered: "(filtrado de _MAX_ totales)",
                paginate: { first: "«", previous: "‹", next: "›", last: "»" }
            }
        });
    }
});

// ── Auto-generar referencia desde el nombre ────────────────────
document.addEventListener('DOMContentLoaded', function () {
    const nombreInput = document.querySelector('#formCrear input[name="nombre"]');
    const refInput    = document.getElementById('crear_referencia');
    if (nombreInput && refInput) {
        nombreInput.addEventListener('input', function () {
            if (!refInput.dataset.manual) {
                const palabras = this.value.trim().split(/\s+/);
                const siglas   = palabras.map(p => p[0] || '').join('').toUpperCase().substring(0, 3);
                const total    = {{ $lotes->count() + 1 }};
                const numero   = String(total).padStart(3, '0');
                refInput.value = siglas ? `${siglas}-${numero}` : '';
            }
        });
        refInput.addEventListener('input', function () {
            this.dataset.manual = 'true';
        });
    }
});

// ── Reabrir modal crear si hay errores de validación ───────────
@if($errors->any() && old('_method') === null)
    openModal('modalCrear');
@endif
</script>
@endsection
