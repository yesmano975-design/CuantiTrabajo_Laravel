<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CuantiTrabajo | Iniciar Sesión</title>
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
                        'glow': '0 0 35px -5px rgba(34, 197, 94, 0.35)',
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1,h2,h3,h4,h5,h6,.font-display { font-family: 'Outfit', sans-serif; }

        .carousel-slide {
            position: absolute; inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1.2s ease-in-out, transform 1.2s ease-in-out;
            transform: scale(1.05);
        }
        .carousel-slide.active {
            opacity: 1;
            transform: scale(1);
        }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 antialiased selection:bg-brand-500 selection:text-white">

<div class="min-h-screen flex flex-col md:flex-row">

    {{-- Left Showcase Carousel --}}
    <div class="hidden md:flex flex-1 relative bg-forest-dark overflow-hidden flex-col justify-between p-12 lg:p-16">
        
        {{-- Slides Background --}}
        <div class="carousel-slide active" style="background-image:url('{{ asset('img/hero_tractor.png') }}')"></div>
        <div class="carousel-slide" style="background-image:url('{{ asset('img/agri_drone_monitoring.png') }}')"></div>
        <div class="carousel-slide" style="background-image:url('{{ asset('img/agri_workers_tablet.png') }}')"></div>

        {{-- Dark Gradient Overlay --}}
        <div class="absolute inset-0 bg-gradient-to-t from-forest-dark/95 via-forest-dark/55 to-forest-dark/30 z-10"></div>

        {{-- Top Brand --}}
        <div class="relative z-20 flex items-center gap-3.5">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white shadow-glow">
                <i class="fas fa-tractor text-xl"></i>
            </div>
            <div>
                <span class="font-display font-black text-2xl tracking-tight text-white">
                    Cuanti<span class="text-brand-400">Trabajo</span>
                </span>
                <span class="block text-[10px] font-bold text-emerald-300 uppercase tracking-widest -mt-1">
                    Gestión Agrícola Inteligente
                </span>
            </div>
        </div>

        {{-- Bottom Hero Content --}}
        <div class="relative z-20 max-w-lg space-y-6">
            
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/20 backdrop-blur-md border border-emerald-400/30 text-brand-300 text-xs font-bold">
                <i class="fas fa-sparkles"></i> Sistema de Nómina & Campo
            </div>

            <h2 id="heroTitle" class="font-display font-black text-4xl lg:text-5xl text-white leading-tight">
                Potencia el <br><span class="text-brand-400">Campo</span> con Precisión
            </h2>

            <p id="heroText" class="text-slate-200 text-base leading-relaxed">
                Plataforma integral para la gestión de nóminas, jornales y actividades agrícolas en tiempo real.
            </p>

            {{-- Slider Indicators --}}
            <div class="flex items-center gap-2.5 pt-4">
                <button type="button" class="dot h-2 rounded-full transition-all duration-300 bg-brand-400 w-8" onclick="goToSlide(0)"></button>
                <button type="button" class="dot h-2 rounded-full transition-all duration-300 bg-white/40 w-2 hover:bg-white/70" onclick="goToSlide(1)"></button>
                <button type="button" class="dot h-2 rounded-full transition-all duration-300 bg-white/40 w-2 hover:bg-white/70" onclick="goToSlide(2)"></button>
            </div>

        </div>

    </div>

    {{-- Right Login Form --}}
    <div class="w-full md:w-[480px] lg:w-[540px] bg-slate-900 flex flex-col justify-center p-8 sm:p-12 lg:p-16 border-l border-white/10 shadow-2xl relative z-30">
        
        <div class="w-full max-w-md mx-auto space-y-8">
            
            {{-- Mobile Logo --}}
            <div class="md:hidden flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-brand-500 flex items-center justify-center text-slate-950 font-black">
                    <i class="fas fa-tractor text-lg"></i>
                </div>
                <span class="font-display font-black text-2xl text-white">Cuanti<span class="text-brand-400">Trabajo</span></span>
            </div>

            {{-- Form Header --}}
            <div>
                <h1 class="font-display font-black text-3xl text-white tracking-tight">Iniciar Sesión</h1>
                <p class="text-slate-400 text-sm mt-2">
                    Ingresa tus credenciales para administrar trabajadores, lotes y liquidaciones.
                </p>
            </div>

            {{-- Error Flash Alert --}}
            @if($errors->any())
            <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-start gap-3">
                <i class="fas fa-circle-exclamation text-rose-400 text-lg mt-0.5 flex-shrink-0"></i>
                <div>
                    <div class="font-bold text-rose-200">Error de autenticación</div>
                    <div>{{ $errors->first() }}</div>
                </div>
            </div>
            @endif

            {{-- Login Form --}}
            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                
                {{-- Email Input --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                        Correo Electrónico
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="admin@cuantitrabajo.com"
                               class="w-full pl-11 pr-4 py-3.5 rounded-xl bg-slate-800/80 border border-white/10 text-white placeholder-slate-500 text-sm font-medium focus:outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-400/20 transition-all">
                    </div>
                </div>

                {{-- Password Input --}}
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300">
                            Contraseña
                        </label>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-500">
                            <i class="fas fa-lock"></i>
                        </div>
                        <input type="password" id="passwordInput" name="password" required
                               placeholder="••••••••••••"
                               class="w-full pl-11 pr-12 py-3.5 rounded-xl bg-slate-800/80 border border-white/10 text-white placeholder-slate-500 text-sm font-medium focus:outline-none focus:border-brand-400 focus:ring-4 focus:ring-brand-400/20 transition-all">
                        <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-white transition-colors">
                            <i id="eyeIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                        class="w-full py-4 rounded-xl bg-gradient-to-r from-brand-400 via-brand-500 to-brand-600 hover:from-brand-300 hover:to-brand-400 text-slate-950 font-black text-base shadow-glow hover:-translate-y-0.5 active:translate-y-0 transition-all flex items-center justify-center gap-2">
                    <span>Acceder al Sistema</span>
                    <i class="fas fa-arrow-right text-sm"></i>
                </button>

            </form>

            {{-- Help / Back link --}}
            <div class="pt-6 border-t border-white/10 text-center flex items-center justify-between text-xs text-slate-400">
                <a href="{{ url('/') }}" class="hover:text-brand-300 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> Volver a la portada
                </a>
                <span>CuantiTrabajo &copy; {{ date('Y') }}</span>
            </div>

        </div>

    </div>

</div>

<script>
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    const slides = document.querySelectorAll('.carousel-slide');
    const dots   = document.querySelectorAll('.dot');
    const titles = [
        'Potencia el <br><span class="text-brand-400">Campo</span> con Precisión',
        'Controla tus <br><span class="text-brand-400">Lotes</span> con Tecnología',
        'Supervisión y <br><span class="text-brand-400">Nómina</span> al Instante'
    ];
    const texts = [
        'Plataforma integral para la gestión de nóminas, jornales y actividades agrícolas en tiempo real.',
        'Supervisión de parcelas, hectáreas y registro geográfico de labores en campo.',
        'Cero demoras en el pago de cuadrillas. Liquidaciones semanales exactas y transparentes.'
    ];

    let current = 0;
    let timer;

    function goToSlide(index) {
        slides[current].classList.remove('active');
        dots[current].classList.remove('bg-brand-400', 'w-8');
        dots[current].classList.add('bg-white/40', 'w-2');

        current = index;

        slides[current].classList.add('active');
        dots[current].classList.remove('bg-white/40', 'w-2');
        dots[current].classList.add('bg-brand-400', 'w-8');

        document.getElementById('heroTitle').innerHTML = titles[current];
        document.getElementById('heroText').textContent = texts[current];

        clearInterval(timer);
        startTimer();
    }

    function startTimer() {
        timer = setInterval(() => {
            goToSlide((current + 1) % slides.length);
        }, 5000);
    }

    startTimer();
</script>
</body>
</html>
