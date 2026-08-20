@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Tipos de Actividad')

@section('content')

{{-- Tabla Principal --}}
<div class="glass-card overflow-hidden">
    {{-- Card Header --}}
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gradient-to-r from-slate-50/80 to-emerald-50/30">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-forest text-emerald-300 flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-tags"></i>
            </div>
            <div>
                <h2 class="font-display font-bold text-xl text-slate-800">Catálogo de Actividades</h2>
                <p class="text-xs text-slate-500">Definición de conceptos de labor y sus unidades métricas de liquidación</p>
            </div>
        </div>
        <button type="button" onclick="openModal('modalCrear')" class="btn-primary-custom flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Nuevo Tipo</span>
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
            <table id="tiposTable" class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre de la Labor</th>
                        <th>Descripción</th>
                        <th>Unidad de Medida</th>
                        <th>Tarifas Asociadas</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tipos as $tipo)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="font-bold text-slate-400 text-xs">#{{ $tipo->id }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center text-sm font-bold shadow-inner">
                                    <i class="fas fa-hand-holding-seedling"></i>
                                </div>
                                <span class="font-bold text-slate-800">{{ $tipo->nombre }}</span>
                            </div>
                        </td>
                        <td class="text-slate-600 font-medium">
                            {{ $tipo->descripcion ?: '-' }}
                        </td>
                        <td>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 uppercase">
                                <i class="fas fa-ruler text-[10px] text-slate-400"></i>
                                {{ $tipo->unidad_medida }}
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                    <i class="fas fa-coins text-[10px]"></i>
                                    {{ $tipo->valor_actividades_count }} tarifa(s)
                                </span>
                                <a href="{{ route('tarifas.create') }}?tipo={{ $tipo->id }}"
                                   class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-all flex items-center justify-center shadow-sm"
                                   title="Crear tarifa para este tipo">
                                    <i class="fas fa-plus text-xs"></i>
                                </a>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Editar --}}
                                <button type="button"
                                    onclick="openEditModal({{ $tipo->id }}, '{{ addslashes($tipo->nombre) }}', '{{ addslashes($tipo->descripcion) }}', '{{ $tipo->unidad_medida }}')"
                                    class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                    title="Editar Tipo">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                </button>

                                {{-- Eliminar --}}
                                <form action="{{ route('tipo-actividades.destroy', $tipo) }}"
                                      method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                        title="Eliminar Tipo"
                                        onclick="return swConfirm(this, '¿Eliminar este tipo de actividad?', 'warning', 'Sí, eliminar')">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-10 text-slate-400">
                            <i class="fas fa-tags text-3xl mb-2 block text-slate-300"></i>
                            No hay tipos de actividad registrados.
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
                    <h3 class="font-display font-bold text-lg text-slate-800">Nuevo Tipo de Actividad</h3>
                    <p class="text-xs text-slate-500">Registrar un nuevo concepto de labor agrícola</p>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalCrear')"
                class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition-colors flex items-center justify-center">
                <i class="fas fa-xmark"></i>
            </button>
        </div>

        {{-- Form --}}
        <form id="formCrear" action="{{ route('tipo-actividades.store') }}" method="POST" class="p-6 space-y-5">
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

            {{-- Nombre --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Nombre de la Labor <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                    placeholder="Ej: Fumigación, Recolección, Poda..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-sm text-slate-800 placeholder-slate-400">
            </div>

            {{-- Descripción --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Descripción <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                </label>
                <textarea name="descripcion" rows="2"
                    placeholder="Descripción breve de la actividad..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-sm text-slate-800 placeholder-slate-400 resize-none">{{ old('descripcion') }}</textarea>
            </div>

            {{-- Unidad de Medida --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Unidad de Medida <span class="text-rose-500">*</span>
                </label>
                <select name="unidad_medida" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 outline-none transition text-sm text-slate-800 bg-white">
                    <option value="" disabled {{ old('unidad_medida') ? '' : 'selected' }}>Seleccionar unidad...</option>
                    <option value="horas"     {{ old('unidad_medida') === 'horas'     ? 'selected' : '' }}>⏱ Horas</option>
                    <option value="dias"      {{ old('unidad_medida') === 'dias'      ? 'selected' : '' }}>📅 Días</option>
                    <option value="hectareas" {{ old('unidad_medida') === 'hectareas' ? 'selected' : '' }}>🌾 Hectáreas</option>
                </select>
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal('modalCrear')"
                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition text-sm font-medium">
                    Cancelar
                </button>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-floppy-disk"></i> Guardar Tipo
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
                    <h3 class="font-display font-bold text-lg text-slate-800">Editar Tipo de Actividad</h3>
                    <p class="text-xs text-slate-500">Modificar el concepto de labor</p>
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

            {{-- Nombre --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Nombre de la Labor <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="edit_nombre" name="nombre" required
                    placeholder="Ej: Fumigación, Recolección, Poda..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm text-slate-800 placeholder-slate-400">
            </div>

            {{-- Descripción --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Descripción <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                </label>
                <textarea id="edit_descripcion" name="descripcion" rows="2"
                    placeholder="Descripción breve de la actividad..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm text-slate-800 placeholder-slate-400 resize-none"></textarea>
            </div>

            {{-- Unidad de Medida --}}
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Unidad de Medida <span class="text-rose-500">*</span>
                </label>
                <select id="edit_unidad" name="unidad_medida" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 outline-none transition text-sm text-slate-800 bg-white">
                    <option value="horas">⏱ Horas</option>
                    <option value="dias">📅 Días</option>
                    <option value="hectareas">🌾 Hectáreas</option>
                </select>
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
function openEditModal(id, nombre, descripcion, unidad_medida) {
    document.getElementById('formEditar').action = '/tipo-actividades/' + id;
    document.getElementById('edit_nombre').value      = nombre;
    document.getElementById('edit_descripcion').value = descripcion;
    document.getElementById('edit_unidad').value      = unidad_medida;
    openModal('modalEditar');
}

// ── DataTable ──────────────────────────────────────────────────
$(document).ready(function () {
    if ($('#tiposTable tbody tr').length > 1 || !$('#tiposTable tbody tr td[colspan]').length) {
        $('#tiposTable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Buscar tipo de actividad...",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ tipos",
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
