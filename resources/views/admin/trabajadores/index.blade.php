@extends('layouts.sidebaradmin')

@section('tituloPagina', 'Trabajadores')

@section('content')

{{-- Resumen de Métricas --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="glass-card p-5 border-l-4 border-emerald-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Operarios</span>
            <div class="text-3xl font-display font-black text-slate-800 mt-1">{{ $trabajadores->count() }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-users"></i>
        </div>
    </div>
    <div class="glass-card p-5 border-l-4 border-blue-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Activos en Campo</span>
            <div class="text-3xl font-display font-black text-blue-600 mt-1">{{ $trabajadores->where('estado','activo')->count() }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-user-check"></i>
        </div>
    </div>
    <div class="glass-card p-5 border-l-4 border-rose-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Inactivos / Pausados</span>
            <div class="text-3xl font-display font-black text-rose-600 mt-1">{{ $trabajadores->where('estado','inactivo')->count() }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-user-times"></i>
        </div>
    </div>
    <div class="glass-card p-5 border-l-4 border-amber-500 flex items-center justify-between">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Cargos Asignados</span>
            <div class="text-3xl font-display font-black text-amber-600 mt-1">{{ $cargos->count() }}</div>
        </div>
        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-sm">
            <i class="fas fa-id-badge"></i>
        </div>
    </div>
</div>

{{-- Tabla Principal --}}
<div class="glass-card overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-gradient-to-r from-slate-50/80 to-emerald-50/30">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-forest text-emerald-300 flex items-center justify-center text-lg shadow-sm">
                <i class="fas fa-hard-hat"></i>
            </div>
            <div>
                <h2 class="font-display font-bold text-xl text-slate-800">Listado de Trabajadores</h2>
                <p class="text-xs text-slate-500">Gestión de personal de campo, cargos y estado operativo</p>
            </div>
        </div>
        <button onclick="openModal('modalCrear')" class="btn-primary-custom flex items-center gap-2">
            <i class="fas fa-plus"></i>
            <span>Nuevo Trabajador</span>
        </button>
    </div>

    <div class="p-6">
        <div class="overflow-x-auto">
            <table id="trabajadoresTable" class="w-full text-sm text-left border-collapse">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Trabajador</th>
                        <th>Documento</th>
                        <th>Cargo</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($trabajadores as $t)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="font-bold text-slate-400 text-xs">#{{ $t->id }}</td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-700 font-bold text-xs uppercase shadow-inner">
                                    {{ substr($t->nombre,0,1) }}{{ substr($t->apellido??'',0,1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800">{{ $t->nombre }} {{ $t->apellido }}</div>
                                    <div class="text-xs text-slate-400">{{ $t->correo ?: 'Sin correo' }}</div>
                                </div>
                            </div>
                        </td>
                        <td><span class="font-mono bg-slate-100 px-2 py-1 rounded text-xs text-slate-600 border border-slate-200">{{ $t->documento }}</span></td>
                        <td>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <i class="fas fa-briefcase text-[10px]"></i> {{ $t->cargo->nombre ?? 'Sin Cargo' }}
                            </span>
                        </td>
                        <td>
                            @if($t->telefono)
                                <div class="text-xs text-slate-600 flex items-center gap-1.5 font-mono"><i class="fas fa-phone text-slate-400 text-[10px]"></i> {{ $t->telefono }}</div>
                            @else
                                <span class="text-slate-400 italic text-xs">Sin teléfono</span>
                            @endif
                        </td>
                        <td>
                            @if($t->estado === 'activo')
                                <span class="badge-active"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Activo</span>
                            @else
                                <span class="badge-inactive"><span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Inactivo</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- Editar: abre modal con datos --}}
                                <button type="button"
                                    onclick="openEditModal({{ $t->id }}, '{{ addslashes($t->nombre) }}', '{{ addslashes($t->apellido) }}', '{{ $t->documento }}', '{{ addslashes($t->correo) }}', '{{ $t->telefono }}', {{ $t->cargo_id }}, '{{ $t->estado }}')"
                                    class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm" title="Editar">
                                    <i class="fas fa-pen-to-square text-xs"></i>
                                </button>
                                {{-- Toggle Estado --}}
                                <form action="{{ route('trabajadores.toggleEstado', $t) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg {{ $t->estado==='activo'?'bg-slate-100 text-slate-600 hover:bg-slate-200':'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }} hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                        title="{{ $t->estado==='activo'?'Desactivar':'Activar' }}"
                                        onclick="return swConfirm(this,'¿Cambiar el estado de este trabajador?','question','Sí, cambiar')">
                                        <i class="fas fa-{{ $t->estado==='activo'?'user-slash':'user-check' }} text-xs"></i>
                                    </button>
                                </form>
                                {{-- Eliminar --}}
                                <form action="{{ route('trabajadores.destroy', $t) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 hover:scale-105 transition-all flex items-center justify-center shadow-sm"
                                        title="Eliminar"
                                        onclick="return swConfirm(this,'¿Eliminar este trabajador permanentemente?','warning','Sí, eliminar')">
                                        <i class="fas fa-trash-can text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center py-10 text-slate-400">
                        <i class="fas fa-folder-open text-3xl mb-2 block text-slate-300"></i>
                        No hay trabajadores registrados.
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     MODAL: CREAR TRABAJADOR
     ══════════════════════════════════════════════ --}}
<div id="modalCrear" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modalCrear')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-emerald-50/40 flex items-center justify-between rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-sm">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-base text-slate-800">Nuevo Trabajador</h3>
                    <p class="text-xs text-slate-500">Los campos con (*) son obligatorios</p>
                </div>
            </div>
            <button onclick="closeModal('modalCrear')" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-all flex items-center justify-center">
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>
        {{-- Form --}}
        <form method="POST" action="{{ route('trabajadores.store') }}" class="p-6 space-y-5">
            @csrf
            {{-- Cargo --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Cargo Operativo <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-briefcase"></i></div>
                    <select name="cargo_id" required class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                        <option value="">-- Seleccionar Cargo --</option>
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->id }}" {{ old('cargo_id')==$cargo->id?'selected':'' }}>{{ $cargo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                @error('cargo_id')<p class="text-xs text-rose-500 font-semibold mt-1"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>@enderror
            </div>
            {{-- Nombre & Apellido --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Nombres <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-user"></i></div>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Juan Carlos"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                    @error('nombre')<p class="text-xs text-rose-500 font-semibold mt-1"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>@enderror
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Apellidos</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-user-group"></i></div>
                        <input type="text" name="apellido" value="{{ old('apellido') }}" placeholder="Ej. Pérez Gómez"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                </div>
            </div>
            {{-- Documento --}}
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Documento de Identidad <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-id-card"></i></div>
                    <input type="text" name="documento" value="{{ old('documento') }}" required placeholder="Cédula de ciudadanía"
                           class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                </div>
                @error('documento')<p class="text-xs text-rose-500 font-semibold mt-1"><i class="fas fa-circle-exclamation"></i> {{ $message }}</p>@enderror
            </div>
            {{-- Correo & Teléfono --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Correo (Opcional)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-envelope"></i></div>
                        <input type="email" name="correo" value="{{ old('correo') }}" placeholder="ejemplo@correo.com"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Teléfono (Opcional)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-phone"></i></div>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" placeholder="300 123 4567"
                               class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm">
                    </div>
                </div>
            </div>
            {{-- Footer --}}
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('modalCrear')" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-sm transition-all">
                    <i class="fas fa-xmark mr-1"></i> Cancelar
                </button>
                <button type="submit" class="btn-primary-custom flex items-center gap-2">
                    <i class="fas fa-check"></i> Guardar Trabajador
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════
     MODAL: EDITAR TRABAJADOR
     ══════════════════════════════════════════════ --}}
<div id="modalEditar" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal('modalEditar')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-amber-50/40 flex items-center justify-between rounded-t-2xl">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center shadow-sm">
                    <i class="fas fa-user-pen"></i>
                </div>
                <div>
                    <h3 class="font-display font-bold text-base text-slate-800">Editar Trabajador</h3>
                    <p class="text-xs text-slate-500" id="editSubtitle">Actualizando registro</p>
                </div>
            </div>
            <button onclick="closeModal('modalEditar')" class="w-8 h-8 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-all flex items-center justify-center">
                <i class="fas fa-xmark text-lg"></i>
            </button>
        </div>
        <form id="formEditar" method="POST" action="" class="p-6 space-y-5">
            @csrf @method('PUT')
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Cargo Operativo <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-briefcase"></i></div>
                    <select name="cargo_id" id="edit_cargo_id" required class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->id }}">{{ $cargo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Nombres <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-user"></i></div>
                        <input type="text" name="nombre" id="edit_nombre" required class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Apellidos</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-user-group"></i></div>
                        <input type="text" name="apellido" id="edit_apellido" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                    </div>
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Documento <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-id-card"></i></div>
                    <input type="text" name="documento" id="edit_documento" required class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Correo</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-envelope"></i></div>
                        <input type="email" name="correo" id="edit_correo" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Teléfono</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-phone"></i></div>
                        <input type="text" name="telefono" id="edit_telefono" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium font-mono focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                    </div>
                </div>
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Estado</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400"><i class="fas fa-toggle-on"></i></div>
                    <select name="estado" id="edit_estado" class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-200 bg-white text-slate-800 text-sm font-medium focus:outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeModal('modalEditar')" class="px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-bold text-sm transition-all">
                    <i class="fas fa-xmark mr-1"></i> Cancelar
                </button>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm transition-all shadow-md flex items-center gap-2">
                    <i class="fas fa-floppy-disk"></i> Actualizar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('js')
<script>
// ── Helpers de modal ────────────────────────────────────────
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}
function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}
// Cerrar con Escape
document.addEventListener('keydown', e => { if(e.key==='Escape') { closeModal('modalCrear'); closeModal('modalEditar'); }});

// ── Abrir modal de edición con datos del trabajador ─────────
function openEditModal(id, nombre, apellido, documento, correo, telefono, cargo_id, estado) {
    document.getElementById('formEditar').action = '/trabajadores/' + id;
    document.getElementById('editSubtitle').textContent = 'Actualizando registro #' + id;
    document.getElementById('edit_nombre').value    = nombre;
    document.getElementById('edit_apellido').value  = apellido;
    document.getElementById('edit_documento').value = documento;
    document.getElementById('edit_correo').value    = correo;
    document.getElementById('edit_telefono').value  = telefono;
    document.getElementById('edit_cargo_id').value  = cargo_id;
    document.getElementById('edit_estado').value    = estado;
    openModal('modalEditar');
}

// ── DataTable ───────────────────────────────────────────────
$(document).ready(function () {
    if ($('#trabajadoresTable tbody tr td:not([colspan])').length) {
        $('#trabajadoresTable').DataTable({
            responsive: true,
            language: {
                search: "_INPUT_", searchPlaceholder: "Buscar trabajador...",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ trabajadores",
                infoEmpty: "Sin registros", infoFiltered: "(filtrado de _MAX_)",
                paginate: { first:"«", previous:"‹", next:"›", last:"»" }
            }
        });
    }
});

// ── Abrir modal si hay errores de validación en crear ───────
@if($errors->any() && old('_method') === null)
    openModal('modalCrear');
@endif
</script>
@endsection
