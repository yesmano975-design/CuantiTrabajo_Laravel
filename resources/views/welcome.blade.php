<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CuantiTrabajo — Plataforma Inteligente de Gestión Agrícola y Nómina en Campo</title>
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
                        },
                        gold: '#F59E0B',
                    },
                    fontFamily: {
                        sans:    ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    boxShadow: {
                        'glow': '0 0 40px -10px rgba(34, 197, 94, 0.35)',
                        'gold-glow': '0 0 35px -5px rgba(245, 158, 11, 0.3)',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1,h2,h3,h4,h5,h6,.font-display { font-family: 'Outfit', sans-serif; }
        
        .hero-pattern {
            background-color: #081C15;
            background-image: radial-gradient(rgba(34, 197, 94, 0.15) 1px, transparent 1px), radial-gradient(rgba(34, 197, 94, 0.08) 1px, #081C15 1px);
            background-size: 40px 40px;
            background-position: 0 0, 20px 20px;
        }

        .glass-panel {
            background: rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 antialiased overflow-x-hidden">

    {{-- TOP NAVIGATION --}}
    <nav class="fixed top-0 inset-x-0 z-50 bg-forest-dark/85 backdrop-blur-lg border-b border-white/10 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            
            {{-- Brand Logo --}}
            <a href="#" class="flex items-center gap-3.5 group">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white shadow-glow group-hover:scale-105 transition-transform">
                    <i class="fas fa-tractor text-xl"></i>
                </div>
                <div>
                    <span class="font-display font-black text-2xl tracking-tight text-white group-hover:text-brand-300 transition-colors">
                        Cuanti<span class="text-brand-400">Trabajo</span>
                    </span>
                    <span class="block text-[10px] font-bold text-emerald-300/80 uppercase tracking-widest -mt-1">
                        Agritech Intelligence
                    </span>
                </div>
            </a>

            {{-- Nav Links --}}
            <div class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-300">
                <a href="#modulos" class="hover:text-brand-300 transition-colors">Módulos</a>
                <a href="#soluciones" class="hover:text-brand-300 transition-colors">Soluciones</a>
                <a href="#beneficios" class="hover:text-brand-300 transition-colors">Beneficios</a>
                <a href="#galeria" class="hover:text-brand-300 transition-colors">En Campo</a>
            </div>

            {{-- Action Button --}}
            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-brand-500 to-brand-600 text-white font-bold text-sm hover:shadow-glow hover:-translate-y-0.5 transition-all shadow-md">
                        <i class="fas fa-grid-2"></i> Ir al Panel
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2.5 px-6 py-2.5 rounded-full bg-gradient-to-r from-brand-400 via-brand-500 to-brand-600 text-slate-950 font-bold text-sm hover:shadow-glow hover:-translate-y-0.5 transition-all shadow-lg">
                        <i class="fas fa-arrow-right-to-bracket text-xs"></i> Ingresar al Sistema
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- HERO SECTION WITH VIDEO BACKGROUND --}}
    <section class="relative min-h-screen pt-32 pb-20 flex items-center justify-center overflow-hidden bg-forest-dark">
        
        {{-- Background Video Container --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
            {{-- Lighter, Transparent Overlay for Vibrant & Clear Video Visibility --}}
            <div class="absolute inset-0 bg-gradient-to-b from-forest-dark/40 via-forest-dark/25 to-slate-950/80 z-10"></div>
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-transparent via-forest-dark/20 to-forest-dark/70 z-10"></div>

            {{-- Local HTML5 Video (Plays instantly when file is placed in public/video/) --}}
            <video 
                id="local-hero-video"
                autoplay 
                muted 
                loop 
                playsinline 
                preload="auto"
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 min-w-full min-h-full w-auto h-auto object-cover pointer-events-none opacity-0 transition-opacity duration-700">
                <source src="{{ asset('video/hero_bg.mp4') }}" type="video/mp4">
                <source src="{{ asset('video/background.mp4') }}" type="video/mp4">
                <source src="{{ asset('video/video.mp4') }}" type="video/mp4">
                <source src="{{ asset('video/hero.mp4') }}" type="video/mp4">
            </video>

            {{-- YouTube Video Background --}}
            <iframe 
                id="hero-bg-video"
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 min-w-full min-h-full w-[300%] h-[300%] sm:w-[200%] sm:h-[200%] lg:w-[150%] lg:h-[150%] object-cover pointer-events-none opacity-85 transition-opacity duration-700 -z-10"
                src="https://www.youtube-nocookie.com/embed/7czWIk0laGI?autoplay=1&mute=1&loop=1&playlist=7czWIk0laGI&controls=0&showinfo=0&rel=0&iv_load_policy=3&disablekb=1&modestbranding=1&playsinline=1&enablejsapi=1"
                title="CuantiTrabajo Video Background"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen>
            </iframe>
        </div>

        {{-- Background Glow Blobs --}}
        <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[500px] bg-brand-500/20 blur-[140px] pointer-events-none rounded-full z-10"></div>
        <div class="absolute top-1/3 right-10 w-[400px] h-[400px] bg-amber-500/15 blur-[120px] pointer-events-none rounded-full z-10"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 w-full">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                
                {{-- Left Text Column --}}
                <div class="lg:col-span-7 space-y-8 text-center lg:text-left">
                    
                    {{-- Badge --}}
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass-panel border border-brand-400/30 text-brand-300 text-xs font-bold uppercase tracking-wider shadow-sm backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-brand-400 animate-ping"></span>
                        <i class="fas fa-seedling text-brand-400"></i>
                        Software Agrícola de Precisión & Liquidación
                    </div>

                    {{-- Headline --}}
                    <h1 class="font-display font-black text-4xl sm:text-5xl lg:text-6xl tracking-tight leading-[1.08] text-white drop-shadow-[0_4px_14px_rgba(0,0,0,0.85)]">
                        Control Total de tus <br>
                        <span class="bg-gradient-to-r from-brand-300 via-brand-400 to-emerald-200 bg-clip-text text-transparent">
                            Jornales, Lotes y Pagos
                        </span>
                    </h1>

                    {{-- Subheading --}}
                    <p class="text-slate-200 text-base sm:text-lg leading-relaxed max-w-2xl mx-auto lg:mx-0 font-normal drop-shadow-[0_2px_8px_rgba(0,0,0,0.8)]">
                        Optimiza la administración de tu finca en tiempo real. Registra labores de abonadores, fumigadores, tractoristas y regadores, valida subtotales y liquida nóminas semanales sin errores.
                    </p>

                    {{-- CTA Action Buttons --}}
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ route('login') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-gradient-to-r from-brand-400 to-brand-500 hover:from-brand-300 hover:to-brand-400 text-slate-950 font-black text-base shadow-glow hover:-translate-y-1 transition-all">
                            <i class="fas fa-tractor text-lg"></i>
                            Comenzar Ahora
                            <i class="fas fa-arrow-right text-xs ml-1"></i>
                        </a>
                        <a href="#modulos"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-4 rounded-2xl glass-panel hover:bg-white/10 text-white font-bold text-base border border-white/15 hover:border-white/30 transition-all">
                            <i class="fas fa-layer-group text-emerald-400"></i>
                            Conocer Módulos
                        </a>
                    </div>

                    {{-- Trust Indicators --}}
                    <div class="pt-6 border-t border-white/10 grid grid-cols-3 gap-6 max-w-lg mx-auto lg:mx-0">
                        <div>
                            <div class="font-display font-black text-2xl text-brand-300">100%</div>
                            <div class="text-xs text-slate-400 font-medium">Control en Campo</div>
                        </div>
                        <div>
                            <div class="font-display font-black text-2xl text-brand-300">0%</div>
                            <div class="text-xs text-slate-400 font-medium">Errores en Nómina</div>
                        </div>
                        <div>
                            <div class="font-display font-black text-2xl text-brand-300">24/7</div>
                            <div class="text-xs text-slate-400 font-medium">Disponibilidad</div>
                        </div>
                    </div>

                </div>

                {{-- Right Interactive Visual Card --}}
                <div class="lg:col-span-5 relative">
                    
                    {{-- Main Visual Container --}}
                    <div class="relative rounded-3xl overflow-hidden glass-panel p-3 border border-white/15 shadow-2xl">
                        
                        {{-- Image with Overlay --}}
                        <div class="relative rounded-2xl overflow-hidden aspect-[4/3] group">
                            <img src="{{ asset('img/hero_tractor.png') }}"
                                 alt="Maquinaria y gestión agrícola"
                                 class="w-full h-full object-cover object-center group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-forest-dark/90 via-forest-dark/30 to-transparent"></div>
                            
                            <div class="absolute bottom-4 left-4 right-4">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/30 backdrop-blur-md text-brand-300 text-xs font-bold border border-emerald-400/30 mb-1.5">
                                    <i class="fas fa-check-circle"></i> Sistema Activo
                                </div>
                                <h3 class="font-display font-bold text-lg text-white">Monitoreo y Liquidación CuantiTrabajo</h3>
                                <p class="text-xs text-slate-300">Trazabilidad completa por trabajador y lote de siembra</p>
                            </div>
                        </div>

                        {{-- Quick Mock Metrics Row --}}
                        <div class="grid grid-cols-2 gap-3 mt-3">
                            <div class="p-3.5 rounded-xl bg-white/5 border border-white/10">
                                <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                                    <i class="fas fa-calculator text-brand-400"></i> Liquidación Semanal
                                </div>
                                <div class="font-display font-bold text-lg text-emerald-300">Cálculo Automático</div>
                                <div class="text-[11px] text-slate-400">Tarifa × Pasadas × Cantidad</div>
                            </div>
                            <div class="p-3.5 rounded-xl bg-white/5 border border-white/10">
                                <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                                    <i class="fas fa-map-pin text-gold"></i> Control de Hectáreas
                                </div>
                                <div class="font-display font-bold text-lg text-amber-300">Multi-Lote</div>
                                <div class="text-[11px] text-slate-400">Referencias y geo-registro</div>
                            </div>
                        </div>

                    </div>

                    {{-- Floating Pill 1 --}}
                    <div class="absolute -top-6 -right-4 floating hidden sm:flex items-center gap-3 p-3.5 rounded-2xl glass-panel border border-brand-400/40 shadow-glow bg-slate-900/90 backdrop-blur-xl">
                        <div class="w-10 h-10 rounded-xl bg-brand-500/20 text-brand-400 flex items-center justify-center font-bold text-lg">
                            <i class="fas fa-badge-check"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white">Validación al Instante</div>
                            <div class="text-[10px] text-emerald-300">Actividades Confirmadas</div>
                        </div>
                    </div>

                    {{-- Floating Pill 2 --}}
                    <div class="absolute -bottom-6 -left-4 floating hidden sm:flex items-center gap-3 p-3.5 rounded-2xl glass-panel border border-amber-400/40 shadow-gold-glow bg-slate-900/90 backdrop-blur-xl" style="animation-delay: -3s;">
                        <div class="w-10 h-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold text-lg">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <div class="text-xs font-bold text-white">Comprobantes PDF / Print</div>
                            <div class="text-[10px] text-amber-300">Listo para nómina</div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    {{-- SHOWCASE CAROUSEL / FIELD MONITORING SECTION --}}
    <section id="galeria" class="py-20 bg-slate-950 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="text-brand-400 font-bold text-xs uppercase tracking-widest">Tecnología de Punta</span>
                <h2 class="font-display font-black text-3xl sm:text-4xl text-white mt-2">
                    Diseñado para la Realidad del Campo Colombiano
                </h2>
                <p class="text-slate-400 text-sm sm:text-base mt-3">
                    Desde el monitoreo aéreo y mecanizado hasta la supervisión directa de cuadrillas de trabajadores.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                
                {{-- Card 1 --}}
                <div class="rounded-3xl overflow-hidden glass-panel border border-white/10 group hover:border-brand-400/50 transition-all duration-300 hover:-translate-y-1.5">
                    <div class="h-56 overflow-hidden relative">
                        <img src="{{ asset('img/agri_drone_monitoring.png') }}"
                             alt="Monitoreo inteligente con drones"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                        <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur-md text-emerald-300 text-xs font-bold border border-emerald-400/30">
                            <i class="fas fa-drone mr-1"></i> Precisión
                        </span>
                    </div>
                    <div class="p-6">
                        <h3 class="font-display font-bold text-xl text-white group-hover:text-brand-300 transition-colors">
                            Monitoreo de Lotes y Terrenos
                        </h3>
                        <p class="text-slate-400 text-xs sm:text-sm mt-2 leading-relaxed">
                            Organiza hectáreas, coordenadas y tipos de cultivo para una asignación clara del personal diario.
                        </p>
                    </div>
                </div>

                {{-- Card 2 --}}
                <div class="rounded-3xl overflow-hidden glass-panel border border-white/10 group hover:border-brand-400/50 transition-all duration-300 hover:-translate-y-1.5">
                    <div class="h-56 overflow-hidden relative">
                        <img src="{{ asset('img/hero_tractor.png') }}"
                             alt="Tractoristas y maquinaria pesada"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                        <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur-md text-amber-300 text-xs font-bold border border-amber-400/30">
                            <i class="fas fa-tractor mr-1"></i> Maquinaria
                        </span>
                    </div>
                    <div class="p-6">
                        <h3 class="font-display font-bold text-xl text-white group-hover:text-brand-300 transition-colors">
                            Control de Pasadas y Horas
                        </h3>
                        <p class="text-slate-400 text-xs sm:text-sm mt-2 leading-relaxed">
                            Registra el número de pasadas en preparación de suelo, rastrillado y nivelación de terrenos.
                        </p>
                    </div>
                </div>

                {{-- Card 3 --}}
                <div class="rounded-3xl overflow-hidden glass-panel border border-white/10 group hover:border-brand-400/50 transition-all duration-300 hover:-translate-y-1.5">
                    <div class="h-56 overflow-hidden relative">
                        <img src="{{ asset('img/agri_workers_tablet.png') }}"
                             alt="Supervisión digital en campo"
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                        <span class="absolute top-4 left-4 px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur-md text-cyan-300 text-xs font-bold border border-cyan-400/30">
                            <i class="fas fa-tablet-screen-button mr-1"></i> Digital
                        </span>
                    </div>
                    <div class="p-6">
                        <h3 class="font-display font-bold text-xl text-white group-hover:text-brand-300 transition-colors">
                            Supervisión y Nómina
                        </h3>
                        <p class="text-slate-400 text-xs sm:text-sm mt-2 leading-relaxed">
                            Cero papeles en el bolsillo del mayordomo: ingresa datos desde celular o computador y aprueba con un clic.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- CORE MODULES GRID --}}
    <section id="modulos" class="py-24 bg-[#0a1a12] relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-brand-400 font-bold text-xs uppercase tracking-widest">Ecosistema Integral</span>
                <h2 class="font-display font-black text-3xl sm:text-4xl text-white mt-2">
                    Módulos Especializados de CuantiTrabajo
                </h2>
                <p class="text-slate-300 text-sm sm:text-base mt-3">
                    Cada aspecto de tu producción agrícola conectado en un único flujo de trabajo intuitivo.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Módulo 1: Trabajadores --}}
                <div class="p-7 rounded-3xl glass-panel border border-white/10 hover:border-brand-400/40 transition-all duration-300 hover:-translate-y-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white text-2xl mb-6 shadow-glow group-hover:scale-110 transition-transform">
                        <i class="fas fa-users-gear"></i>
                    </div>
                    <h3 class="font-display font-bold text-xl text-white mb-2 group-hover:text-brand-300 transition-colors">
                        Gestión de Trabajadores
                    </h3>
                    <p class="text-slate-300 text-sm leading-relaxed mb-4">
                        Ficha completa de operarios con cargos especializados (Abonador, Tractorista, Fumigador, Regador), documentos y estados activos.
                    </p>
                    <div class="flex items-center text-xs font-bold text-brand-400 group-hover:translate-x-1 transition-transform">
                        <span>Ver más detalles</span> <i class="fas fa-chevron-right text-[10px] ml-1.5"></i>
                    </div>
                </div>

                {{-- Módulo 2: Lotes --}}
                <div class="p-7 rounded-3xl glass-panel border border-white/10 hover:border-brand-400/40 transition-all duration-300 hover:-translate-y-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-400 to-blue-600 flex items-center justify-center text-white text-2xl mb-6 shadow-md group-hover:scale-110 transition-transform">
                        <i class="fas fa-map-location-dot"></i>
                    </div>
                    <h3 class="font-display font-bold text-xl text-white mb-2 group-hover:text-cyan-300 transition-colors">
                        Control de Lotes & Terrenos
                    </h3>
                    <p class="text-slate-300 text-sm leading-relaxed mb-4">
                        Administración de predios, nomenclatura de referencia, registro de hectáreas y seguimiento histórico de labores por lote.
                    </p>
                    <div class="flex items-center text-xs font-bold text-cyan-400 group-hover:translate-x-1 transition-transform">
                        <span>Ver más detalles</span> <i class="fas fa-chevron-right text-[10px] ml-1.5"></i>
                    </div>
                </div>

                {{-- Módulo 3: Actividades --}}
                <div class="p-7 rounded-3xl glass-panel border border-white/10 hover:border-brand-400/40 transition-all duration-300 hover:-translate-y-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-600 flex items-center justify-center text-white text-2xl mb-6 shadow-md group-hover:scale-110 transition-transform">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h3 class="font-display font-bold text-xl text-white mb-2 group-hover:text-amber-300 transition-colors">
                        Actividades Laborales
                    </h3>
                    <p class="text-slate-300 text-sm leading-relaxed mb-4">
                        Captura diaria de labores, cálculo de subtotales dinámicos (tarifa × cantidad × pasadas) y validación de estados.
                    </p>
                    <div class="flex items-center text-xs font-bold text-amber-400 group-hover:translate-x-1 transition-transform">
                        <span>Ver más detalles</span> <i class="fas fa-chevron-right text-[10px] ml-1.5"></i>
                    </div>
                </div>

                {{-- Módulo 4: Tarifas Dinámicas --}}
                <div class="p-7 rounded-3xl glass-panel border border-white/10 hover:border-brand-400/40 transition-all duration-300 hover:-translate-y-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-400 to-indigo-600 flex items-center justify-center text-white text-2xl mb-6 shadow-md group-hover:scale-110 transition-transform">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h3 class="font-display font-bold text-xl text-white mb-2 group-hover:text-purple-300 transition-colors">
                        Tarifas y Unidades
                    </h3>
                    <p class="text-slate-300 text-sm leading-relaxed mb-4">
                        Configuración de precios por unidad de medida (jornal, hectárea, bulto) con vigencias por fecha para garantizar pagos justos.
                    </p>
                    <div class="flex items-center text-xs font-bold text-purple-400 group-hover:translate-x-1 transition-transform">
                        <span>Ver más detalles</span> <i class="fas fa-chevron-right text-[10px] ml-1.5"></i>
                    </div>
                </div>

                {{-- Módulo 5: Pagos Semanales --}}
                <div class="p-7 rounded-3xl glass-panel border border-white/10 hover:border-brand-400/40 transition-all duration-300 hover:-translate-y-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white text-2xl mb-6 shadow-glow group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h3 class="font-display font-bold text-xl text-white mb-2 group-hover:text-emerald-300 transition-colors">
                        Liquidación Semanal
                    </h3>
                    <p class="text-slate-300 text-sm leading-relaxed mb-4">
                        Cierre de ciclo semanal por trabajador con desglose detallado, generación de comprobantes y listos para desembolso.
                    </p>
                    <div class="flex items-center text-xs font-bold text-emerald-400 group-hover:translate-x-1 transition-transform">
                        <span>Ver más detalles</span> <i class="fas fa-chevron-right text-[10px] ml-1.5"></i>
                    </div>
                </div>

                {{-- Módulo 6: Seguridad & Roles --}}
                <div class="p-7 rounded-3xl glass-panel border border-white/10 hover:border-brand-400/40 transition-all duration-300 hover:-translate-y-2 group">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-rose-400 to-red-600 flex items-center justify-center text-white text-2xl mb-6 shadow-md group-hover:scale-110 transition-transform">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <h3 class="font-display font-bold text-xl text-white mb-2 group-hover:text-rose-300 transition-colors">
                        Seguridad & Roles
                    </h3>
                    <p class="text-slate-300 text-sm leading-relaxed mb-4">
                        Protección total de datos, permisos segregados para Administradores y Secretarias, y trazabilidad de acciones.
                    </p>
                    <div class="flex items-center text-xs font-bold text-rose-400 group-hover:translate-x-1 transition-transform">
                        <span>Ver más detalles</span> <i class="fas fa-chevron-right text-[10px] ml-1.5"></i>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- HOW IT WORKS STEP SECTION --}}
    <section id="soluciones" class="py-24 bg-slate-900 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-brand-400 font-bold text-xs uppercase tracking-widest">Flujo Simplificado</span>
                <h2 class="font-display font-black text-3xl sm:text-4xl text-white mt-2">
                    ¿Cómo Funciona CuantiTrabajo?
                </h2>
                <p class="text-slate-400 text-sm sm:text-base mt-3">
                    En 3 sencillos pasos transformas el desorden de libretas en un sistema digital transparente.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 relative">
                
                {{-- Step 1 --}}
                <div class="p-8 rounded-3xl bg-slate-800/60 border border-white/10 text-center relative">
                    <div class="w-12 h-12 rounded-2xl bg-brand-500/20 text-brand-300 font-display font-black text-xl flex items-center justify-center mx-auto mb-6 border border-brand-400/30">
                        1
                    </div>
                    <h3 class="font-display font-bold text-xl text-white mb-3">Registra la Labor</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Selecciona el trabajador, el lote y el tipo de actividad. El sistema calcula al vuelo el subtotal según tarifa vigente.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="p-8 rounded-3xl bg-slate-800/60 border border-white/10 text-center relative">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-300 font-display font-black text-xl flex items-center justify-center mx-auto mb-6 border border-amber-400/30">
                        2
                    </div>
                    <h3 class="font-display font-bold text-xl text-white mb-3">Valida y Confirma</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        El administrador revisa las cantidades y valida las actividades para habilitarlas en la liquidación semanal.
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="p-8 rounded-3xl bg-slate-800/60 border border-white/10 text-center relative">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/20 text-emerald-300 font-display font-black text-xl flex items-center justify-center mx-auto mb-6 border border-emerald-400/30">
                        3
                    </div>
                    <h3 class="font-display font-bold text-xl text-white mb-3">Liquida e Imprime</h3>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Genera con un solo clic el reporte de pago semanal por trabajador, listo para pagar e imprimir comprobantes.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- CTA BANNER --}}
    <section class="py-20 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-gradient-to-r from-forest-dark via-forest to-forest-light p-10 sm:p-16 border border-brand-400/30 shadow-2xl relative overflow-hidden text-center lg:text-left flex flex-col lg:flex-row items-center justify-between gap-8">
                
                {{-- Background Light --}}
                <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-brand-400/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="max-w-2xl relative z-10 space-y-4">
                    <h2 class="font-display font-black text-3xl sm:text-4xl text-white">
                        ¿Listo para llevar tu finca al siguiente nivel?
                    </h2>
                    <p class="text-emerald-100/80 text-sm sm:text-base">
                        Ingresa a tu cuenta y comienza a registrar actividades, controlar lotes y gestionar la nómina de tus trabajadores hoy mismo.
                    </p>
                </div>

                <div class="relative z-10 flex-shrink-0">
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-3 px-8 py-4 rounded-2xl bg-white text-forest font-black text-base hover:bg-brand-50 hover:shadow-glow hover:scale-105 transition-all shadow-xl">
                        <i class="fas fa-arrow-right-to-bracket text-emerald-600"></i>
                        Ingresar a CuantiTrabajo
                    </a>
                </div>

            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-forest-dark border-t border-white/10 py-12 text-slate-400 text-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-6">
            
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-brand-500 flex items-center justify-center text-slate-950 font-black">
                    <i class="fas fa-tractor text-sm"></i>
                </div>
                <span class="font-display font-bold text-white text-base">CuantiTrabajo</span>
                <span class="text-xs text-slate-500">| © {{ date('Y') }} Todos los derechos reservados.</span>
            </div>

            <div class="flex items-center gap-6 text-xs text-slate-400">
                <span class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-brand-400"></span> Servidor Operativo
                </span>
                <span>Laravel v{{ Illuminate\Foundation\Application::VERSION }}</span>
            </div>

        </div>
    </footer>

    {{-- Video Handler: Smooth Fade-in without Play Button / Flash --}}
    <script src="https://www.youtube-nocookie.com/iframe_api"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const localVideo = document.getElementById('local-hero-video');
            const ytIframe = document.getElementById('hero-bg-video');

            if (localVideo) {
                // If local video starts playing, fade it in and hide YouTube iframe
                localVideo.addEventListener('playing', () => {
                    localVideo.classList.remove('opacity-0');
                    localVideo.classList.add('opacity-90');
                    if (ytIframe) ytIframe.style.display = 'none';
                });
                
                // Attempt autoplay
                const playPromise = localVideo.play();
                if (playPromise !== undefined) {
                    playPromise.catch(() => {
                        // Fallback to YouTube iframe
                    });
                }
            }
        });

        let ytBgPlayer;
        function onYouTubeIframeAPIReady() {
            ytBgPlayer = new YT.Player('hero-bg-video', {
                events: {
                    'onReady': function(event) {
                        event.target.mute();
                        event.target.playVideo();
                    },
                    'onStateChange': function(event) {
                        const localVideo = document.getElementById('local-hero-video');
                        const ytIframe = document.getElementById('hero-bg-video');

                        // Show YouTube video ONLY when active PLAYING state is reached (eliminates Play icon splash)
                        if (event.data === YT.PlayerState.PLAYING) {
                            if (ytIframe && (!localVideo || localVideo.paused || localVideo.readyState < 3)) {
                                ytIframe.classList.remove('opacity-0');
                                ytIframe.classList.add('opacity-85');
                            }
                        } else if (event.data === YT.PlayerState.ENDED) {
                            event.target.playVideo();
                        }
                    }
                }
            });
        }
    </script>

</body>
</html>

