<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CuantiTrabajo — @yield('tituloPagina', 'Panel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#f0fdf4',
                            100: '#dcfce7',
                            200: '#bbf7d0',
                            300: '#86efac',
                            400: '#4ade80',
                            500: '#22c55e',
                            600: '#16a34a',
                            700: '#15803d',
                            800: '#166534',
                            900: '#14532d',
                            950: '#052e16',
                        },
                        forest: {
                            DEFAULT: '#1B4332',
                            light:   '#2D6A4F',
                            dark:    '#081C15',
                            deep:    '#04130d',
                        },
                        sage:  '#52796F',
                        mint:  '#D8F3DC',
                        amber: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        }
                    },
                    fontFamily: {
                        sans:    ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    boxShadow: {
                        'glow-brand': '0 0 20px -3px rgba(34, 197, 94, 0.25)',
                        'card-soft': '0 4px 20px -2px rgba(15, 23, 42, 0.05)',
                        'card-hover': '0 12px 30px -4px rgba(27, 67, 50, 0.12)',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f8f6; color: #1e293b; }
        h1,h2,h3,h4,h5,h6,.font-display { font-family: 'Outfit', sans-serif; }

        /* Sidebar scrollbar */
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,.3); }

        /* Submenu animation */
        .submenu { max-height: 0; overflow: hidden; transition: max-height .3s cubic-bezier(0.4, 0, 0.2, 1); }
        .submenu.open { max-height: 400px; }
        .chevron { transition: transform .3s ease; }
        .chevron.open { transform: rotate(180deg); }

        /* Active nav item */
        .nav-item-active {
            background: linear-gradient(90deg, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0.08) 100%) !important;
            color: #ffffff !important;
            box-shadow: inset 4px 0 0 #4ade80, 0 4px 12px rgba(0,0,0,0.1);
        }
        .nav-item-active i { color: #86efac !important; }

        /* Card glass effect */
        .glass-card {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.9);
        }

        /* Global scrollbars */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f0f7f4; }
        ::-webkit-scrollbar-thumb { background: #86efac; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #2D6A4F; }

        /* DataTables Custom Tailwind Integration */
        .dataTables_wrapper {
            padding: 0.5rem 0;
            font-size: 0.875rem;
        }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1.25rem;
            color: #475569;
            font-weight: 500;
        }
        .dataTables_wrapper .dataTables_filter {
            float: right;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 0.75rem !important;
            padding: 0.5rem 1rem 0.5rem 2rem !important;
            outline: none !important;
            background: #ffffff !important;
            font-size: 0.875rem !important;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'%3E%3C/path%3E%3C/svg%3E") !important;
            background-repeat: no-repeat !important;
            background-position: 0.6rem center !important;
            background-size: 1rem 1rem !important;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #16a34a !important;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.15) !important;
        }
        .dataTables_wrapper .dataTables_length select {
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 0.6rem !important;
            padding: 0.35rem 1.75rem 0.35rem 0.75rem !important;
            outline: none !important;
            background-color: #fff !important;
        }

        /* Modern Table styling */
        table.dataTable {
            border-collapse: separate !important;
            border-spacing: 0 !important;
            width: 100% !important;
            margin-top: 0.5rem !important;
            margin-bottom: 1rem !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.85rem !important;
            overflow: hidden !important;
        }
        table.dataTable thead th {
            background: #f8fafc !important;
            color: #334155 !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            border-bottom: 1.5px solid #e2e8f0 !important;
            border-top: none !important;
            padding: 0.9rem 1rem !important;
        }
        table.dataTable tbody tr {
            transition: background 0.15s ease;
        }
        table.dataTable tbody tr:nth-of-type(even) {
            background-color: #fbfdfc;
        }
        table.dataTable tbody tr:hover {
            background-color: #f0fdf4 !important;
        }
        table.dataTable tbody td {
            padding: 0.85rem 1rem !important;
            vertical-align: middle !important;
            border-bottom: 1px solid #f1f5f9 !important;
            color: #334155;
            font-size: 0.875rem;
        }

        /* DataTables export buttons */
        .dt-buttons {
            margin-bottom: 1rem;
            display: inline-flex;
            gap: 0.35rem;
        }
        .dt-buttons .dt-button {
            background: #ffffff !important;
            color: #1e293b !important;
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 0.65rem !important;
            padding: 0.45rem 0.9rem !important;
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
            transition: all 0.2s !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 0.35rem !important;
        }
        .dt-buttons .dt-button:hover {
            background: #f0fdf4 !important;
            border-color: #86efac !important;
            color: #166534 !important;
            transform: translateY(-1px);
        }

        /* Pagination */
        .dataTables_wrapper .dataTables_paginate {
            margin-top: 1rem;
            display: flex;
            justify-content: flex-end;
            gap: 0.25rem;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.5rem !important;
            padding: 0.35rem 0.75rem !important;
            border: 1px solid #e2e8f0 !important;
            background: #fff !important;
            color: #475569 !important;
            font-size: 0.825rem !important;
            font-weight: 600 !important;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background: #1B4332 !important;
            border-color: #1B4332 !important;
            color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(27, 67, 50, 0.25);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f0fdf4 !important;
            border-color: #86efac !important;
            color: #15803d !important;
        }
        .dataTables_wrapper .dataTables_info {
            margin-top: 1rem;
            font-size: 0.8rem;
            color: #64748b;
        }
    </style>
    @yield('css')
</head>
<body class="flex h-screen overflow-hidden antialiased text-slate-800">

{{-- Backdrop for mobile sidebar --}}
<div id="sidebar-backdrop"
     onclick="toggleSidebar()"
     class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-30 hidden transition-opacity lg:hidden">
</div>

{{-- ======================================================
     SIDEBAR
     ====================================================== --}}
<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-40 w-64 flex flex-col h-screen bg-gradient-to-b from-forest-dark via-forest to-forest-light shadow-2xl transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:flex-shrink-0">

    {{-- Logo & Brand --}}
    <div class="flex items-center justify-between px-5 py-5 border-b border-white/10">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center flex-shrink-0 shadow-glow-brand group-hover:scale-105 transition-transform">
                <i class="fas fa-tractor text-white text-lg"></i>
            </div>
            <div>
                <div class="font-display font-black text-white text-lg tracking-tight leading-none group-hover:text-brand-300 transition-colors">
                    CuantiTrabajo
                </div>
                <div class="text-[11px] font-semibold text-brand-300/80 tracking-wider uppercase mt-1">
                    Gestión Agrícola
                </div>
            </div>
        </a>
        <button onclick="toggleSidebar()" class="lg:hidden text-white/70 hover:text-white p-1 rounded-lg">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>

    {{-- Navigation Menu --}}
    <nav class="flex-1 overflow-y-auto sidebar-scroll py-4 px-3 space-y-1.5">

        <div class="px-3 pb-2 text-[10px] font-bold tracking-wider text-emerald-200/50 uppercase">
            Principal
        </div>

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition-all group font-medium text-sm
                  {{ request()->routeIs('dashboard') ? 'nav-item-active' : '' }}">
            <i class="fas fa-grid-2 text-base w-5 text-center group-hover:text-brand-300 {{ request()->routeIs('dashboard') ? 'text-brand-300' : 'text-emerald-200/70' }}"></i>
            <span>Dashboard</span>
        </a>

        <div class="px-3 pt-3 pb-1 text-[10px] font-bold tracking-wider text-emerald-200/50 uppercase">
            Operaciones de Campo
        </div>

        {{-- Trabajadores --}}
        <div>
            <button onclick="toggleMenu('menu-trabajadores', 'chev-trabajadores')"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition-all group font-medium text-sm
                       {{ request()->routeIs('trabajadores.*') ? 'nav-item-active' : '' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-user-group text-base w-5 text-center group-hover:text-brand-300 {{ request()->routeIs('trabajadores.*') ? 'text-brand-300' : 'text-emerald-200/70' }}"></i>
                    <span>Trabajadores</span>
                </div>
                <i id="chev-trabajadores" class="fas fa-chevron-down text-xs text-white/50 chevron {{ request()->routeIs('trabajadores.*') ? 'open' : '' }}"></i>
            </button>
            <div id="menu-trabajadores" class="submenu pl-8 mt-1 space-y-1 {{ request()->routeIs('trabajadores.*') ? 'open' : '' }}">
                <a href="{{ route('trabajadores.create') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 text-xs font-medium transition-all
                          {{ request()->routeIs('trabajadores.create') ? 'text-brand-300 bg-white/10 font-bold' : '' }}">
                    <i class="fas fa-user-plus text-[10px] w-3.5"></i> Registrar Nuevo
                </a>
                <a href="{{ route('trabajadores.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 text-xs font-medium transition-all
                          {{ request()->routeIs('trabajadores.index') ? 'text-brand-300 bg-white/10 font-bold' : '' }}">
                    <i class="fas fa-table-list text-[10px] w-3.5"></i> Listado General
                </a>
            </div>
        </div>

        {{-- Lotes --}}
        <div>
            <button onclick="toggleMenu('menu-lotes', 'chev-lotes')"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition-all group font-medium text-sm
                       {{ request()->routeIs('lotes.*') ? 'nav-item-active' : '' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-map-location-dot text-base w-5 text-center group-hover:text-brand-300 {{ request()->routeIs('lotes.*') ? 'text-brand-300' : 'text-emerald-200/70' }}"></i>
                    <span>Lotes y Terrenos</span>
                </div>
                <i id="chev-lotes" class="fas fa-chevron-down text-xs text-white/50 chevron {{ request()->routeIs('lotes.*') ? 'open' : '' }}"></i>
            </button>
            <div id="menu-lotes" class="submenu pl-8 mt-1 space-y-1 {{ request()->routeIs('lotes.*') ? 'open' : '' }}">
                <a href="{{ route('lotes.create') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 text-xs font-medium transition-all
                          {{ request()->routeIs('lotes.create') ? 'text-brand-300 bg-white/10 font-bold' : '' }}">
                    <i class="fas fa-plus text-[10px] w-3.5"></i> Registrar Lote
                </a>
                <a href="{{ route('lotes.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 text-xs font-medium transition-all
                          {{ request()->routeIs('lotes.index') ? 'text-brand-300 bg-white/10 font-bold' : '' }}">
                    <i class="fas fa-layer-group text-[10px] w-3.5"></i> Mapa de Lotes
                </a>
            </div>
        </div>

        {{-- Actividades Laborales --}}
        <div>
            <button onclick="toggleMenu('menu-acts', 'chev-acts')"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition-all group font-medium text-sm
                       {{ request()->routeIs('actividades.*') ? 'nav-item-active' : '' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-clipboard-check text-base w-5 text-center group-hover:text-brand-300 {{ request()->routeIs('actividades.*') ? 'text-brand-300' : 'text-emerald-200/70' }}"></i>
                    <span>Actividades</span>
                </div>
                <i id="chev-acts" class="fas fa-chevron-down text-xs text-white/50 chevron {{ request()->routeIs('actividades.*') ? 'open' : '' }}"></i>
            </button>
            <div id="menu-acts" class="submenu pl-8 mt-1 space-y-1 {{ request()->routeIs('actividades.*') ? 'open' : '' }}">
                <a href="{{ route('actividades.create') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 text-xs font-medium transition-all
                          {{ request()->routeIs('actividades.create') ? 'text-brand-300 bg-white/10 font-bold' : '' }}">
                    <i class="fas fa-plus text-[10px] w-3.5"></i> Registrar Diaria
                </a>
                <a href="{{ route('actividades.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 text-xs font-medium transition-all
                          {{ request()->routeIs('actividades.index') ? 'text-brand-300 bg-white/10 font-bold' : '' }}">
                    <i class="fas fa-list-check text-[10px] w-3.5"></i> Historial y Validación
                </a>
            </div>
        </div>

        <div class="px-3 pt-3 pb-1 text-[10px] font-bold tracking-wider text-emerald-200/50 uppercase">
            Finanzas y Tarifas
        </div>

        {{-- Pagos --}}
        <a href="{{ route('pagos.index') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition-all group font-medium text-sm
                  {{ request()->routeIs('pagos.*') ? 'nav-item-active' : '' }}">
            <i class="fas fa-file-invoice-dollar text-base w-5 text-center group-hover:text-brand-300 {{ request()->routeIs('pagos.*') ? 'text-brand-300' : 'text-emerald-200/70' }}"></i>
            <span class="flex-1">Liquidación Pagos</span>
            <span class="text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-400/30 px-1.5 py-0.5 rounded-md">Semanal</span>
        </a>

        {{-- Tarifas y Valores --}}
        <div>
            <button onclick="toggleMenu('menu-tarifas', 'chev-tarifas')"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition-all group font-medium text-sm
                       {{ request()->routeIs('tarifas.*') || request()->routeIs('tipo-actividades.*') ? 'nav-item-active' : '' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-tags text-base w-5 text-center group-hover:text-brand-300 {{ request()->routeIs('tarifas.*') || request()->routeIs('tipo-actividades.*') ? 'text-brand-300' : 'text-emerald-200/70' }}"></i>
                    <span>Tarifas y Valores</span>
                </div>
                <i id="chev-tarifas" class="fas fa-chevron-down text-xs text-white/50 chevron {{ request()->routeIs('tarifas.*') || request()->routeIs('tipo-actividades.*') ? 'open' : '' }}"></i>
            </button>
            <div id="menu-tarifas" class="submenu pl-8 mt-1 space-y-1 {{ request()->routeIs('tarifas.*') || request()->routeIs('tipo-actividades.*') ? 'open' : '' }}">
                <a href="{{ route('tipo-actividades.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 text-xs font-medium transition-all
                          {{ request()->routeIs('tipo-actividades.*') ? 'text-brand-300 bg-white/10 font-bold' : '' }}">
                    <i class="fas fa-shapes text-[10px] w-3.5"></i> Tipos de Actividad
                </a>
                <a href="{{ route('tarifas.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 text-xs font-medium transition-all
                          {{ request()->routeIs('tarifas.*') ? 'text-brand-300 bg-white/10 font-bold' : '' }}">
                    <i class="fas fa-dollar-sign text-[10px] w-3.5"></i> Tarifas Vigentes
                </a>
            </div>
        </div>

        <div class="px-3 pt-3 pb-1 text-[10px] font-bold tracking-wider text-emerald-200/50 uppercase">
            Administración
        </div>

        {{-- Usuarios --}}
        <div>
            <button onclick="toggleMenu('menu-users', 'chev-users')"
                class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl text-white/80 hover:bg-white/10 hover:text-white transition-all group font-medium text-sm
                       {{ request()->routeIs('usuarios.*') ? 'nav-item-active' : '' }}">
                <div class="flex items-center gap-3">
                    <i class="fas fa-shield-halved text-base w-5 text-center group-hover:text-brand-300 {{ request()->routeIs('usuarios.*') ? 'text-brand-300' : 'text-emerald-200/70' }}"></i>
                    <span>Usuarios y Roles</span>
                </div>
                <i id="chev-users" class="fas fa-chevron-down text-xs text-white/50 chevron {{ request()->routeIs('usuarios.*') ? 'open' : '' }}"></i>
            </button>
            <div id="menu-users" class="submenu pl-8 mt-1 space-y-1 {{ request()->routeIs('usuarios.*') ? 'open' : '' }}">
                <a href="{{ route('usuarios.create') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 text-xs font-medium transition-all
                          {{ request()->routeIs('usuarios.create') ? 'text-brand-300 bg-white/10 font-bold' : '' }}">
                    <i class="fas fa-user-plus text-[10px] w-3.5"></i> Nuevo Usuario
                </a>
                <a href="{{ route('usuarios.index') }}"
                   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-white/70 hover:text-white hover:bg-white/10 text-xs font-medium transition-all
                          {{ request()->routeIs('usuarios.index') ? 'text-brand-300 bg-white/10 font-bold' : '' }}">
                    <i class="fas fa-users-gear text-[10px] w-3.5"></i> Lista de Usuarios
                </a>
            </div>
        </div>

    </nav>

    {{-- Footer Sidebar / User Card --}}
    <div class="p-3 border-t border-white/10 bg-black/15">
        <div class="flex items-center gap-3 p-2 rounded-xl bg-white/5 border border-white/10">
            <div class="relative">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-brand-600 to-brand-400 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                    {{ strtoupper(substr(Auth::user()->nombre ?? 'A', 0, 1)) }}
                </div>
                <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-400 border-2 border-forest-dark rounded-full"></span>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-xs font-bold text-white truncate">{{ Auth::user()->nombre ?? 'Admin' }} {{ Auth::user()->apellido ?? '' }}</div>
                <div class="text-[11px] text-emerald-300/80 truncate">{{ Auth::user()->rol->nombre ?? 'Administrador' }}</div>
            </div>
        </div>
    </div>
</aside>

{{-- ======================================================
     MAIN VIEWPORT AREA
     ====================================================== --}}
<div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-[#f3f7f5]">

    {{-- Sticky Modern Topbar --}}
    <header class="flex-shrink-0 h-16 bg-white/90 backdrop-blur-md border-b border-slate-200/80 flex items-center justify-between px-4 sm:px-6 shadow-xs z-20">
        <div class="flex items-center gap-3">
            <button onclick="toggleSidebar()"
                class="p-2 rounded-xl hover:bg-slate-100 transition text-slate-600 lg:hidden focus:outline-none">
                <i class="fas fa-bars-staggered text-lg"></i>
            </button>
            <div class="hidden sm:block">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
                    <span>CuantiTrabajo</span>
                    <i class="fas fa-chevron-right text-[9px] text-slate-300"></i>
                    <span class="text-emerald-700 font-bold">@yield('tituloPagina', 'Panel')</span>
                </div>
                <h1 class="font-display font-black text-slate-800 text-lg leading-tight tracking-tight">
                    @yield('tituloPagina', 'Panel de Control')
                </h1>
            </div>
        </div>

        {{-- Right Topbar Actions --}}
        <div class="flex items-center gap-3 sm:gap-4">
            <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200/60 text-xs font-semibold text-emerald-800">
                <i class="fas fa-calendar-day text-emerald-600"></i>
                <span>{{ now()->locale('es')->isoFormat('dddd, D MMMM YYYY') }}</span>
            </div>

            <a href="{{ route('actividades.create') }}"
               class="inline-flex items-center gap-2 bg-gradient-to-r from-forest to-forest-light text-white px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-bold hover:shadow-lg hover:shadow-forest/20 hover:-translate-y-0.5 transition-all shadow-sm">
                <i class="fas fa-plus-circle text-brand-300"></i>
                <span class="hidden sm:inline">Nueva Actividad</span>
                <span class="sm:hidden">Nueva</span>
            </a>

            <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>

            {{-- User Dropdown --}}
            <div class="relative" id="user-menu-container">
                <button onclick="toggleUserMenu()"
                    class="flex items-center gap-2.5 p-1 rounded-xl hover:bg-slate-100 transition-all focus:outline-none group">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100/80 border border-emerald-300/60 flex items-center justify-center text-forest font-black text-sm shadow-xs">
                        {{ strtoupper(substr(Auth::user()->nombre ?? 'A', 0, 1)) }}
                    </div>
                    <div class="hidden xl:block text-left leading-tight">
                        <div class="text-xs font-bold text-slate-700 truncate max-w-[120px]">{{ Auth::user()->nombre ?? 'Admin' }}</div>
                        <div class="text-[10px] text-slate-400 font-medium">{{ Auth::user()->rol->nombre ?? 'Usuario' }}</div>
                    </div>
                    <i class="fas fa-chevron-down text-[10px] text-slate-400 hidden xl:block group-hover:text-slate-600 transition-colors"></i>
                </button>

                {{-- Dropdown panel --}}
                <div id="user-dropdown"
                     class="absolute right-0 top-full mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50 hidden">
                    <div class="px-4 py-2 border-b border-slate-100 mb-1">
                        <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->nombre ?? 'Admin' }} {{ Auth::user()->apellido ?? '' }}</p>
                        <p class="text-[10px] text-slate-400 font-medium truncate">{{ Auth::user()->rol->nombre ?? 'Administrador' }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition-colors rounded-xl mx-0">
                            <i class="fas fa-arrow-right-from-bracket w-4"></i>
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content Container --}}
    <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto space-y-6">
            @yield('content')
        </div>
    </main>

</div>

{{-- SweetAlert Alerts --}}
@if(session('success'))
<script>
    Swal.fire({
        icon: 'success',
        title: '¡Operación Exitosa!',
        text: '{{ session('success') }}',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true,
        toast: true,
        position: 'top-end',
        background: '#ffffff',
        iconColor: '#16a34a',
        customClass: {
            popup: 'rounded-2xl shadow-xl border border-emerald-100'
        }
    });
</script>
@endif
@if(session('error'))
<script>
    Swal.fire({
        icon: 'error',
        title: 'Atención',
        text: '{{ session('error') }}',
        confirmButtonColor: '#1B4332',
        customClass: {
            popup: 'rounded-2xl shadow-xl'
        }
    });
</script>
@endif

<script>
function toggleMenu(menuId, chevId) {
    const menu = document.getElementById(menuId);
    const chev = document.getElementById(chevId);
    if(menu) menu.classList.toggle('open');
    if(chev) chev.classList.toggle('open');
}

function toggleUserMenu() {
    const dropdown = document.getElementById('user-dropdown');
    if (dropdown) dropdown.classList.toggle('hidden');
}

document.addEventListener('click', function(e) {
    const container = document.getElementById('user-menu-container');
    const dropdown  = document.getElementById('user-dropdown');
    if (container && dropdown && !container.contains(e.target)) {
        dropdown.classList.add('hidden');
    }
});

/**
 * Reemplaza el confirm() nativo con SweetAlert2.
 * Uso: onclick="swConfirm(this, '¿Mensaje?')"
 *   o para forms: swConfirm(this, '¿Mensaje?', 'warning', 'Sí, eliminar', 'Cancelar')
 */
function swConfirm(el, message, icon, confirmText, cancelText) {
    icon        = icon        || 'question';
    confirmText = confirmText || 'Sí, continuar';
    cancelText  = cancelText  || 'Cancelar';

    Swal.fire({
        title: message,
        icon: icon,
        showCancelButton: true,
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        confirmButtonColor: icon === 'warning' ? '#ef4444' : '#1B4332',
        cancelButtonColor:  '#64748b',
        reverseButtons: true,
        customClass: {
            popup:         'rounded-2xl shadow-2xl',
            confirmButton: 'rounded-xl font-bold text-sm px-5 py-2.5',
            cancelButton:  'rounded-xl font-bold text-sm px-5 py-2.5',
        }
    }).then(function(result) {
        if (result.isConfirmed) {
            // Si el elemento es un botón dentro de un form, hace submit
            // Si es un link/botón con form, busca el form padre
            const form = el.closest('form');
            if (form) {
                form.submit();
            } else if (el.tagName === 'A') {
                window.location.href = el.href;
            }
        }
    });
    return false; // Siempre cancela el evento nativo
}

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
    }
}
</script>

{{-- DataTables Dependencies --}}
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

@yield('js')
</body>
</html>

