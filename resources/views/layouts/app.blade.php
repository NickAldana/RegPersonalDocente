<!DOCTYPE html>
<html lang="es" class="h-full">
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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">

    <style>
        /* =========================================================
           SIA - SISTEMA INTEGRADO DE ACREDITACIÓN
           IDENTIDAD CORPORATIVA: UPDS SANTA CRUZ
           Versión 4.0 - Arquitectura Modular Namespaced
           ========================================================= */

        :root {
            /* --- PALETA EJECUTIVA UPDS --- */
            --upds-blue: #003566;
            --upds-blue-dark: #001d3d;
            --upds-blue-light: #00509d;
            --upds-gold: #ffc300;
            --upds-gray-bg: #f8fafc;
            --upds-input-bg: #f8fafc;
            
            /* --- TOKENS DE DISEÑO --- */
            --sia-shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --sia-shadow-lg: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            --sia-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ---------------------------------------------------------
           1. CORE & LAYOUT (.sia-core-)
           --------------------------------------------------------- */
        body {
            background-color: var(--upds-gray-bg);
            letter-spacing: -0.01em;
            font-family: 'Inter', system-ui, sans-serif;
            color: #334155;
        }

        [x-cloak] { display: none !important; }

        /* Scrollbar personalizado */
        .sia-core-scrollbar::-webkit-scrollbar { width: 5px; }
        .sia-core-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .sia-core-scrollbar::-webkit-scrollbar-thumb { 
            background: rgba(0, 53, 102, 0.2); 
            border-radius: 10px; 
        }

        /* ---------------------------------------------------------
           2. NAVEGACIÓN INSTITUCIONAL (.sia-nav-)
           --------------------------------------------------------- */
        .sia-nav-sidebar {
            background-color: var(--upds-blue) !important;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            background-image: linear-gradient(180deg, var(--upds-blue) 0%, var(--upds-blue-dark) 100%);
        }

        /* Tarjeta de Perfil Superior */
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

        /* Enlaces de Menú */
        .sia-nav-link {
            color: rgba(255, 255, 255, 0.65) !important;
            transition: var(--sia-transition);
            border-radius: 8px;
            display: flex;
            align-items: center;
            padding: 10px 12px;
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
            margin-bottom: 4px;
        }

        .sia-nav-link:hover {
            background-color: rgba(255, 255, 255, 0.06) !important;
            color: white !important;
            transform: translateX(4px);
        }

        .sia-nav-link-active {
            background-color: var(--upds-gold) !important;
            color: var(--upds-blue) !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 12px rgba(255, 195, 0, 0.3);
        }
        
        /* ---------------------------------------------------------
           3. HEADER SUPERIOR (.sia-header-)
           --------------------------------------------------------- */
        .sia-header {
            background-color: white;
            border-bottom: 1px solid #e2e8f0;
            box-shadow: var(--sia-shadow-sm);
            height: 70px;
        }

        /* ---------------------------------------------------------
           4. COMPONENTES UI (.sia-ui-)
           --------------------------------------------------------- */
        
        /* Indicador Live Pulse */
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

<body class="h-screen overflow-hidden" x-data="{ sidebarOpen: true }">

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

    {{-- SIDEBAR ESCRITORIO (Estilo Identidad 4.0) --}}
    <aside x-show="sidebarOpen" 
           x-transition:enter="transition ease-out duration-300"
           x-transition:enter-start="-translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition ease-in duration-300"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="-translate-x-full"
           class="hidden md:flex md:w-72 md:flex-col md:fixed md:inset-y-0 sia-nav-sidebar z-20 shadow-2xl" x-cloak>
        
        <div class="flex flex-col flex-grow pt-6 overflow-y-auto sia-core-scrollbar px-4">
            
            {{-- LOGO UPDS --}}
            <div class="flex items-center flex-shrink-0 mb-8 mt-2 px-2">
                <i class="bi bi-mortarboard-fill text-upds-gold fs-1 me-3 drop-shadow-md"></i>
                <div>
                    <span class="block text-white font-black text-2xl tracking-tighter leading-none">SIA</span>
                    <span class="text-blue-200/80 text-[10px] font-bold uppercase tracking-[0.2em]">Acreditación V4.0</span>
                </div>
            </div>

            {{-- TARJETA DE USUARIO (Rectorado) --}}
            <div class="mb-8">
                <div class="sia-nav-user-card p-4 group relative">
                    <div class="flex items-center">
                        <div class="relative">
                            <img class="h-10 w-10 rounded-lg border border-white/20 object-cover shadow-lg" 
                                 src="{{ (Auth::user()->personal && Auth::user()->personal->FotoPerfil) ? asset('storage/' . Auth::user()->personal->FotoPerfil) : 'https://ui-avatars.com/api/?name='.urlencode(Auth::user()->name).'&background=ffc300&color=003566' }}" 
                                 alt="Avatar">
                            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500 border border-blue-900"></span>
                            </span>
                        </div>
                        
                        <div class="ml-3 overflow-hidden flex-1">
                            <p class="text-xs font-bold text-white truncate leading-tight">{{ explode(' ', Auth::user()->name)[0] }}</p>
                            <p class="text-[10px] font-bold text-upds-gold truncate uppercase tracking-wider mt-0.5">
                                {{ Auth::user()->cargo() ? Auth::user()->cargo()->NombreCargo : 'RECTORADO' }}
                            </p>
                        </div>

                        {{-- Botón Editar Integrado --}}
                        <a href="{{ route('profile.edit') }}" class="ml-1 text-white/40 hover:text-white transition-colors" title="Configurar">
                            <i class="bi bi-gear-fill"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- MENÚ DE NAVEGACIÓN --}}
            <nav class="flex-1 space-y-6">
                
                {{-- Bloque Plataforma --}}
                <div>
                    <p class="px-2 text-[10px] font-black text-blue-300/50 uppercase tracking-widest mb-3">Plataforma</p>
                    
                    <a href="{{ route('dashboard') }}" 
                       class="sia-nav-link {{ request()->routeIs('dashboard') ? 'sia-nav-link-active' : '' }}">
                        <i class="bi bi-grid-1x2-fill mr-3"></i> 
                        Panel de Control
                    </a>
                </div>

                {{-- Bloque Gestión --}}
                @canany(['gestionar_personal', 'asignar_carga'])
                <div>
                    <p class="px-2 text-[10px] font-black text-blue-300/50 uppercase tracking-widest mb-3">Gestión Académica</p>
                    
                    @can('gestionar_personal')
                    <a href="{{ route('personal.index') }}" 
                       class="sia-nav-link {{ request()->routeIs('personal.index') ? 'sia-nav-link-active' : '' }}">
                        <i class="bi bi-people-fill mr-3"></i> 
                        Directorio Personal
                    </a>
                    @endcan

                    @can('asignar_carga')
                    <a href="{{ route('carga.create') }}" 
                       class="sia-nav-link {{ request()->routeIs('carga.*') ? 'sia-nav-link-active' : '' }}">
                        <i class="bi bi-calendar-check-fill mr-3"></i> 
                        Asignación Materias
                    </a>
                    @endcan
                </div>
                @endcanany
        
{{-- BLOQUE ANALÍTICA UNIFICADO --}}
<div x-data="{ open: {{ request()->routeIs('analitica.*') || request()->routeIs('reporte.pdf') ? 'true' : 'false' }} }">
    <p class="px-2 text-[10px] font-black text-blue-300/50 uppercase tracking-widest mb-3 mt-4">Intelligence</p>
    
    <button @click="open = !open" 
            class="sia-nav-link w-full justify-between group {{ request()->routeIs('analitica.*') || request()->routeIs('reporte.pdf') ? 'text-white' : '' }}">
        <div class="flex items-center">
            <i class="bi bi-graph-up-arrow mr-3 text-warning"></i> 
            <span class="font-bold tracking-wide">Power BI</span>
        </div>
        <i class="bi bi-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
    </button>
    
    <div x-show="open" x-cloak class="mt-2 ml-4 pl-3 border-l border-white/10 space-y-1">
        {{-- Reporte 1: Grados Académicos --}}
        <a href="{{ route('analitica.acreditacion') }}" 
           class="sia-nav-link text-xs {{ request()->routeIs('analitica.acreditacion') ? 'sia-nav-link-active' : 'opacity-70 hover:opacity-100' }}">
            <span>Grados Académicos</span>
            <i class="bi bi-mortarboard-fill ms-auto text-[10px] text-cyan-400"></i>
        </a>

        {{-- Reporte 2: Profesión y Contratos --}}
        <a href="{{ route('analitica.powerbi_show') }}" 
           class="sia-nav-link text-xs {{ request()->routeIs('analitica.powerbi_show') ? 'sia-nav-link-active' : 'opacity-70 hover:opacity-100' }}">
            <span>Profesión y Contratos</span>
            <i class="bi bi-briefcase-fill ms-auto text-[10px] text-yellow-400"></i>
        </a>

        {{-- Reporte 3: Presentación Final PDF --}}
        <a href="{{ route('reporte.pdf') }}" 
           class="sia-nav-link text-xs {{ request()->routeIs('reporte.pdf') ? 'sia-nav-link-active' : 'opacity-70 hover:opacity-100' }}">
            <span>Informe Grado Academico</span>
            <i class="bi bi-file-earmark-pdf-fill ms-auto text-[10px] text-red-400"></i>
        </a>
        {{-- Reporte 4: Infografía de Inversión --}}
<a href="{{ route('reporte.inversion') }}" 
   class="sia-nav-link text-xs {{ request()->routeIs('reporte.inversion') ? 'sia-nav-link-active' : 'opacity-70 hover:opacity-100' }}">
    <span>Inversión Profesional</span>
    <i class="bi bi-file-earmark-pdf-fill ms-auto text-[10px] text-info"></i>
</a>
    </div>
</div>
        </nav>

            {{-- Footer Sidebar --}}
            <div class="py-6 mt-auto">
                <div class="text-center">
                    <p class="text-[9px] text-blue-300/40 font-mono">UPDS SCZ © {{ date('Y') }}</p>
                </div>
            </div>
        </div>
    </aside>

    {{-- CONTENIDO PRINCIPAL --}}
    <div class="flex flex-col flex-1 h-screen transition-all duration-300" :class="sidebarOpen ? 'md:pl-72' : 'md:pl-0'">
        
        {{-- HEADER FORMAL --}}
        <header class="sia-header flex px-8 items-center justify-between shrink-0 sticky top-0 z-10">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-upds-blue transition-colors">
                    <i class="bi bi-list text-2xl"></i>
                </button>
                
                {{-- Breadcrumb Ejecutivo --}}
                <div class="hidden sm:block">
                    <h1 class="text-lg font-extrabold text-upds-blue tracking-tight capitalize">{{ request()->segment(1) ?? 'Dashboard' }}</h1>
                    <p class="text-[11px] text-gray-400 font-medium uppercase tracking-wider">Sistema de Gestión Integrado</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <span class="text-xs font-semibold text-gray-400 border px-2 py-1 rounded-md bg-gray-50">{{ date('d M, Y') }}</span>
                <div class="h-6 w-px bg-gray-200"></div>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 text-xs font-bold text-red-500 hover:text-red-700 transition uppercase tracking-wide">
                        <i class="bi bi-power fs-6"></i>
                        <span class="hidden sm:inline">Salir</span>
                    </button>
                </form>
            </div>
        </header>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 overflow-y-auto p-8 sia-core-scrollbar">
            <div class="max-w-7xl mx-auto">
                @yield('content')
            </div>
        </main>
    </div>
    @else
    {{-- LAYOUT LOGIN (Usando tus clases sia-auth) --}}
    <div class="sia-auth-bg w-full h-full items-center justify-center p-4">
        @yield('content')
    </div>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>