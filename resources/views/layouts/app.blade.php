<!DOCTYPE html>
<html lang="es" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIA - UPDS Santa Cruz</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* ESTILOS SIA V4.0 - SE MANTIENEN IDÉNTICOS */
        :root {
            --upds-blue: #003566;
            --upds-blue-dark: #001d3d;
            --upds-blue-light: #00509d;
            --upds-gold: #ffc300;
            --upds-gray-bg: #f8fafc;
            --upds-input-bg: #f8fafc;
            --sia-shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --sia-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            --sia-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            background-color: var(--upds-gray-bg);
            letter-spacing: -0.01em;
            font-family: 'Inter', system-ui, sans-serif;
            color: #1e293b; 
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        [x-cloak] { display: none !important; }

        .sia-core-scrollbar::-webkit-scrollbar { width: 5px; }
        .sia-core-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .sia-core-scrollbar::-webkit-scrollbar-thumb { 
            background: rgba(0, 53, 102, 0.2); 
            border-radius: 10px; 
        }

        .sia-nav-sidebar {
            background-color: var(--upds-blue) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            background-image: linear-gradient(180deg, var(--upds-blue) 0%, var(--upds-blue-dark) 100%);
        }

        .sia-nav-user-card {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            transition: var(--sia-transition);
            border-radius: 12px;
        }

        .sia-nav-user-card:hover {
            border-color: var(--upds-gold);
            background: rgba(0, 0, 0, 0.4);
        }

        .sia-nav-link {
            color: rgba(255, 255, 255, 0.75) !important;
            transition: var(--sia-transition);
            border-radius: 8px;
            display: flex;
            align-items: center;
            padding: 12px 14px;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            margin-bottom: 4px;
        }

        .sia-nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: white !important;
            transform: translateX(4px);
        }

        .sia-nav-link-active {
            background-color: var(--upds-gold) !important;
            color: var(--upds-blue) !important;
            font-weight: 800 !important;
            box-shadow: 0 4px 12px rgba(255, 195, 0, 0.3);
        }
        
        .sia-header {
            background-color: white;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            height: 80px;
        }

        .sia-ui-pulse {
            display: inline-block;
            width: 8px; height: 8px;
            background-color: #fb7185;
            border-radius: 50%;
            position: relative;
        }

        .sia-ui-pulse::after {
            content: ''; position: absolute;
            width: 100%; height: 100%;
            background-color: inherit;
            border-radius: 50%;
            animation: sia-pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }

        @keyframes sia-pulse-ring {
            0% { transform: scale(.33); opacity: 1; }
            80%, 100% { transform: scale(2.5); opacity: 0; }
        }
        
        .text-upds-blue { color: var(--upds-blue); }
        .text-upds-gold { color: var(--upds-gold); }
    </style>
</head>

<body class="h-screen overflow-hidden flex" x-data="{ sidebarOpen: true }">

    @auth
    {{-- SIDEBAR MÓVIL --}}
    <div x-show="sidebarOpen" class="fixed inset-0 z-40 md:hidden" role="dialog" x-cloak>
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm" @click="sidebarOpen = false"></div>
        <div class="relative flex-1 flex flex-col max-w-xs w-full sia-nav-sidebar transition-all duration-300">
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button @click="sidebarOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full text-white ring-2 ring-white">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                <div class="flex-shrink-0 flex items-center px-6 mb-8">
                     <i class="bi bi-mortarboard-fill text-upds-gold fs-2 me-3"></i>
                    <span class="text-white font-black text-xl tracking-tight">SIA - UPDS</span>
                </div>
            </div>
        </div>
    </div>

    {{-- SIDEBAR ESCRITORIO --}}
    <aside x-show="sidebarOpen" 
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-300"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="hidden md:flex md:w-72 md:flex-col md:fixed md:inset-y-0 sia-nav-sidebar z-20 shadow-2xl shrink-0" x-cloak>
        
        <div class="flex flex-col flex-grow pt-6 overflow-y-auto sia-core-scrollbar px-4">
            
            {{-- LOGO UPDS --}}
            <div class="flex items-center flex-shrink-0 mb-8 mt-2 px-2">
                <i class="bi bi-mortarboard-fill text-upds-gold fs-1 me-3 drop-shadow-md"></i>
                <div>
                    <span class="block text-white font-black text-2xl tracking-tighter leading-none">SIA</span>
                    <span class="text-blue-200/80 text-[10px] font-bold uppercase tracking-[0.2em]">Acreditación V4.0</span>
                </div>
            </div>

            {{-- TARJETA DE USUARIO DEL SIDEBAR (Datos V3.1 Sincronizados) --}}
            <div class="mb-8 px-2">
                <div class="bg-[#002855] rounded-2xl p-3 border border-white/10 shadow-lg relative group overflow-hidden">
                    {{-- Efecto de brillo sutil --}}
                    <div class="absolute top-0 right-0 w-20 h-20 bg-blue-500/10 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none"></div>

                    <div class="flex items-center gap-3 relative z-10">
                        {{-- Avatar con Indicador --}}
                        <div class="relative shrink-0">
                            <div class="w-10 h-10 rounded-lg bg-white p-0.5 shadow-sm">
                                @if(auth()->user()->personal && auth()->user()->personal->Fotoperfil)
                                    <img class="w-full h-full rounded-md object-cover" 
                                         src="{{ asset('storage/' . auth()->user()->personal->Fotoperfil) }}?v={{ time() }}" 
                                         alt="Avatar" loading="lazy">
                                @else
                                    <img class="w-full h-full rounded-md object-cover" 
                                         src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->personal->Nombrecompleto ?? 'U') }}&background=ffc300&color=003566&bold=true" 
                                         alt="Avatar" loading="lazy">
                                @endif
                            </div>
                            <span class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 border-2 border-[#002855] rounded-full"></span>
                        </div>
                        
                        {{-- Info Usuario --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-white leading-tight mb-1">
                                {{ auth()->user()->personal ? explode(' ', auth()->user()->personal->Nombrecompleto)[0] : 'Usuario' }}
                            </p>
                            <p class="text-[10px] font-black text-upds-gold uppercase tracking-wider whitespace-normal leading-tight">
                                {{ auth()->user()->personal->cargo->Nombrecargo ?? 'INVITADO' }}
                            </p>
                        </div>

                        {{-- Icono Configuración --}}
                        <div class="self-start">
                            <a href="{{ route('profile.edit') }}" class="text-slate-400 hover:text-white transition-colors p-1">
                                <i class="bi bi-gear-fill text-sm"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MENÚ DE NAVEGACIÓN --}}
            <nav class="flex-1 space-y-2">
                <div>
                    <p class="px-2 text-[10px] font-black text-blue-300/50 uppercase tracking-widest mb-2 mt-2">Plataforma</p>
                    <a href="{{ route('dashboard') }}" class="sia-nav-link {{ request()->routeIs('dashboard') ? 'sia-nav-link-active' : '' }}">
                        <i class="bi bi-grid-1x2-fill mr-3"></i> Panel de Control
                    </a>
                </div>

                @canany(['gestionar_personal', 'asignar_carga'])
                <div>
                    <p class="px-2 text-[10px] font-black text-blue-300/50 uppercase tracking-widest mb-2 mt-4">Gestión Académica</p>
                    @can('gestionar_personal')
                    <a href="{{ route('personal.index') }}" class="sia-nav-link {{ request()->routeIs('personal.index') ? 'sia-nav-link-active' : '' }}">
                        <i class="bi bi-people-fill mr-3"></i> Directorio Personal
                    </a>
                    @endcan
                    @can('asignar_carga')
                    <a href="{{ route('carga.create') }}" class="sia-nav-link {{ request()->routeIs('carga.*') ? 'sia-nav-link-active' : '' }}">
                        <i class="bi bi-calendar-check-fill mr-3"></i> Asignación Materias
                    </a>
                    @endcan
                </div>
                @endcanany

                {{-- ======================================================= --}}
                {{-- NUEVA SECCIÓN: INVESTIGACIÓN                            --}}
                {{-- ======================================================= --}}
                <div>
                    <p class="px-2 text-[10px] font-black text-blue-300/50 uppercase tracking-widest mb-2 mt-4">Investigación</p>
                    {{-- Verifica ambas rutas: el dashboard (index) y el formulario (publicaciones.*) --}}
                    <a href="{{ route('investigacion.index') }}" class="sia-nav-link {{ request()->routeIs('investigacion.*') || request()->routeIs('publicaciones.*') ? 'sia-nav-link-active' : '' }}">
                        <i class="bi bi-journal-bookmark-fill mr-3"></i> Producción Científica
                    </a>
                </div>
        
                <div x-data="{ open: {{ request()->routeIs('analitica.*') || request()->routeIs('reporte.pdf') ? 'true' : 'false' }} }">
                    <p class="px-2 text-[10px] font-black text-blue-300/50 uppercase tracking-widest mb-2 mt-4">Intelligence</p>
                    <button @click="open = !open" class="sia-nav-link w-full justify-between group {{ request()->routeIs('analitica.*') || request()->routeIs('reporte.pdf') ? 'text-white font-bold' : '' }}">
                        <div class="flex items-center">
                            <i class="bi bi-graph-up-arrow mr-3 text-warning"></i> 
                            <span class="tracking-wide">Paneles Indicadores</span>
                        </div>
                        <i class="bi bi-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="open" x-cloak class="mt-1 ml-4 pl-3 border-l border-white/10 space-y-1">
                        <a href="{{ route('analitica.acreditacion') }}" class="sia-nav-link text-xs py-2 {{ request()->routeIs('analitica.acreditacion') ? 'sia-nav-link-active' : 'opacity-80 hover:opacity-100' }}">
                            <span>Indicadores Docente</span>
                            <i class="bi bi-mortarboard-fill ms-auto text-[10px] text-cyan-400"></i>
                        </a>
                        <a href="{{ route('reporte.pdf', ['archivo' => 'reporte_presentacion.pdf', 'titulo' => 'Informe Acreditación']) }}" 
                           class="sia-nav-link text-xs py-2 {{ request()->route('archivo') == 'reporte_presentacion.pdf' ? 'sia-nav-link-active' : 'opacity-80 hover:opacity-100' }}">
                            <span>Informe PDF</span>
                            <i class="bi bi-file-earmark-text-fill ms-auto text-[10px] text-green-400"></i>
                        </a>
                    </div>
                </div>
            </nav>

            <div class="py-6 mt-auto">
                <div class="text-center">
                    <p class="text-[9px] text-blue-300/40 font-mono">UPDS SCZ © {{ date('Y') }}</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="flex flex-col flex-1 h-screen transition-all duration-300 bg-slate-50 w-full" :class="sidebarOpen ? 'md:pl-72' : 'md:pl-0'">
        
        <header class="sia-header flex px-8 items-center justify-between shrink-0 sticky top-0 z-20">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-upds-blue transition-colors p-2 rounded-lg hover:bg-slate-100">
                    <i class="bi bi-list text-2xl"></i>
                </button>
                <div class="hidden sm:block">
                    <h1 class="text-xl font-black text-slate-800 tracking-tight capitalize leading-none">{{ request()->segment(1) ?? 'Dashboard' }}</h1>
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider mt-1">Sistema de Gestión Integrado</p>
                </div>
            </div>

            <div class="flex items-center gap-6">
                <div class="hidden md:flex flex-col items-end">
                    <span class="text-xs font-bold text-slate-700">{{ date('d M, Y') }}</span>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider bg-slate-100 px-2 py-0.5 rounded">Gestión 2026</span>
                </div>
                <div class="h-8 w-px bg-slate-200"></div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold text-red-600 bg-red-50 hover:bg-red-600 hover:text-white transition-all uppercase tracking-wide border border-red-100 hover:border-red-600">
                        <i class="bi bi-power fs-6"></i>
                        <span class="hidden sm:inline">Salir</span>
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto {{ request()->routeIs('reporte.pdf') ? 'p-0' : 'p-8' }} sia-core-scrollbar bg-slate-50">
            <div class="max-w-7xl mx-auto pb-10">
                @yield('content')
            </div>
        </main>
    </div>

    @else
    <div class="w-full h-full flex items-center justify-center p-4 bg-slate-100">
        @yield('content')
    </div>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    
</body>
</html>