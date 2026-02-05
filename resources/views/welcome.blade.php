<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIA UPDS | Modelo AgileQ++</title>
    
    {{-- TAILWIND CSS --}}
    <script src="https://cdn.tailwindcss.com"></script>
    
    {{-- FUENTES E ICONOS --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        upds: {
                            blue: '#003566',
                            dark: '#001d36',
                            gold: '#ffc300',
                            gray: '#f3f4f6'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        .glass-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 53, 102, 0.08);
        }
        
        .hero-bg {
            background-color: #f8fafc;
            background-image: radial-gradient(#003566 0.5px, transparent 0.5px);
            background-size: 24px 24px;
            opacity: 0.6;
        }

        .fade-up {
            animation: fadeUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
            opacity: 0;
            transform: translateY(20px);
        }
        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }
        .delay-200 { animation-delay: 0.2s; }
    </style>
</head>
<body class="font-sans text-slate-600 antialiased selection:bg-upds-gold selection:text-upds-blue bg-white">

    {{-- NAVBAR --}}
    <nav class="fixed w-full z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-24">
                {{-- LOGO --}}
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-upds-blue text-upds-gold flex items-center justify-center rounded-xl shadow-lg">
                        <i class="bi bi-mortarboard-fill text-2xl"></i>
                    </div>
                    <div class="leading-tight">
                        <span class="block font-black text-upds-blue text-xl tracking-tight">UPDS</span>
                        <span class="block text-xs font-bold text-slate-400 uppercase tracking-widest">AgileQ++</span>
                    </div>
                </div>

                {{-- MENÚ --}}
                <div class="hidden lg:flex items-center gap-10">
                    <a href="#inicio" class="text-sm font-bold text-slate-600 hover:text-upds-blue transition-colors">Inicio</a>
                    <a href="#modelo" class="text-sm font-bold text-slate-600 hover:text-upds-blue transition-colors">Modelo</a>
                    <a href="#acreditacion" class="text-sm font-bold text-slate-600 hover:text-upds-blue transition-colors">Acreditación</a>
                    <a href="#indicadores" class="text-sm font-bold text-slate-600 hover:text-upds-blue transition-colors">Indicadores</a>
                </div>

                {{-- ACCIONES DINÁMICAS --}}
                <div class="flex items-center gap-6">
                    <div class="hidden md:block text-right">
                        <span class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Gestión Estratégica</span>
                        <span class="block text-xs font-bold text-upds-blue">Vicerrectorado Académico</span>
                    </div>
                    
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="group relative inline-flex items-center justify-center px-8 py-3 text-sm font-bold text-white transition-all duration-200 bg-upds-blue rounded-full hover:bg-upds-dark shadow-lg hover:-translate-y-0.5">
                                <i class="bi bi-speedometer2 mr-2"></i> Ir al Panel
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="group relative inline-flex items-center justify-center px-8 py-3 text-sm font-bold text-white transition-all duration-200 bg-upds-blue rounded-full hover:bg-upds-dark shadow-lg hover:-translate-y-0.5">
                                Acceso al Sistema
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>
    {{-- HERO SECTION --}}
    <header id="inicio" class="relative pt-40 pb-24 lg:pt-52 lg:pb-40 overflow-hidden">
        <div class="absolute inset-0 hero-bg -z-10"></div>
        
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-20 items-center">
                
                {{-- Contenido Izquierdo --}}
                <div class="fade-up">
                    <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-white border border-blue-100 text-upds-blue text-xs font-bold uppercase tracking-wider mb-8 shadow-sm">
                        <span class="relative flex h-2.5 w-2.5">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                        </span>
                        Gestión Estratégica Universitaria
                    </div>
                    
                    <h1 class="text-6xl lg:text-7xl font-black text-slate-900 leading-[1.05] mb-8 tracking-tight">
                        Modelo <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-upds-blue via-blue-700 to-upds-blue">AgileQ++</span>
                    </h1>
                    
                    <p class="text-xl text-slate-600 mb-10 leading-relaxed max-w-xl font-medium">
                        Optimización y monitoreo de <strong>indicadores de tercera generación</strong> para la excelencia académica y procesos de acreditación internacional basados en evidencia.
                    </p>

                    {{-- BOTÓN DINÁMICO --}}
                    <div class="flex gap-4 mb-12">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex justify-center items-center px-8 py-4 text-base font-bold text-upds-blue bg-upds-gold rounded-xl hover:bg-yellow-400 transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                                <i class="bi bi-speedometer2 mr-2"></i> Continuar al Panel
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex justify-center items-center px-8 py-4 text-base font-bold text-upds-blue bg-upds-gold rounded-xl hover:bg-yellow-400 transition-all shadow-lg hover:shadow-xl hover:-translate-y-1">
                                <i class="bi bi-lightning-charge-fill mr-2"></i> Comenzar Gestión
                            </a>
                        @endauth
                    </div>

                    {{-- VALIDACIÓN INSTITUCIONAL (AJUSTADA PARA LEGIBILIDAD) --}}
                    <div class="pt-8 border-t border-slate-100">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Avalado por Entidades Reguladoras</p>
                        <div class="flex flex-wrap items-center gap-10 opacity-90 hover:opacity-100 transition-opacity">
                            
                            {{-- CIEES - Ajustado --}}
                            <div class="group flex items-center gap-4 bg-white px-5 py-3 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                                <img src="https://www.upds.edu.bo/wp-content/uploads/2025/02/26-02-1-1.jpg" 
                                     alt="Logotipo CIEES" 
                                     class="h-14 w-auto object-contain brightness-110 filter">
                                <div class="leading-tight">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">Certificación</span>
                                    <span class="block text-sm font-black text-upds-blue">CIEES</span>
                                </div>
                            </div>

                            {{-- MERCOSUR - Ajustado --}}
                            <div class="group flex items-center gap-4 bg-white px-5 py-3 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all">
                                <img src="https://th.bing.com/th/id/R.d911541c12c23fc91ee8d2095929fa16?rik=BFeAp6NyxuRXag&riu=http%3a%2f%2fwww.bnm.me.gov.ar%2fnovedades%2fwp-content%2fuploads%2f2016%2f04%2fmercosur_educativo-150x134.png&ehk=OxzSWHtpZC7loQksczR9dHF433mC1ixxCCo0M1nEgps%3d&risl=&pid=ImgRaw&r=0" 
                                     alt="Logotipo Mercosur Educativo" 
                                     class="h-14 w-auto object-contain">
                                <div class="leading-tight">
                                    <span class="block text-[10px] font-bold text-slate-400 uppercase">Acreditación</span>
                                    <span class="block text-sm font-black text-upds-blue">MERCOSUR</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Contenido Derecho: DASHBOARD MOCKUP --}}
                <div class="relative fade-up delay-200 hidden lg:block">
                    <div class="absolute -inset-1 bg-gradient-to-r from-upds-gold via-orange-300 to-upds-blue rounded-3xl blur opacity-30"></div>
                    
                    <div class="relative bg-white rounded-2xl border border-slate-200 shadow-2xl p-8">
                        {{-- Header Dashboard Mockup --}}
                        <div class="flex justify-between items-start mb-8 border-b border-slate-100 pb-6">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Tablero de Control</h3>
                                <p class="text-xs font-medium text-slate-400 mt-1">Gestión Académica 1-2026</p>
                            </div>
                            <div class="flex items-center gap-2 bg-green-50 px-3 py-1 rounded-full border border-green-100">
                                <span class="text-[10px] font-black text-green-600 uppercase tracking-wide">Sincronizado</span>
                                <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                            </div>
                        </div>

                        {{-- Stats Grid --}}
                        <div class="grid grid-cols-2 gap-6 mb-8">
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase mb-2">Carga Académica</p>
                                <p class="text-3xl font-black text-upds-blue">94.2%</p>
                            </div>
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase mb-2">Acreditación</p>
                                <p class="text-3xl font-black text-slate-800">A+</p>
                            </div>
                        </div>

                        {{-- List Mockup --}}
                        <div class="space-y-4">
                            @foreach(['Alineación Estratégica', 'Inteligencia de Datos'] as $item)
                            <div class="flex items-center justify-between p-4 rounded-xl border border-slate-100 bg-slate-50/50">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-upds-blue flex items-center justify-center">
                                        <i class="bi bi-check2-circle"></i>
                                    </div>
                                    <span class="text-sm font-bold text-slate-700">{{ $item }}</span>
                                </div>
                                <i class="bi bi-chevron-right text-slate-300 text-xs"></i>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </header>
    {{-- PILARES DE INNOVACIÓN --}}
    <section id="modelo" class="py-28 bg-slate-50 relative border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <h2 class="text-sm font-black text-upds-gold uppercase tracking-widest mb-3">Pilares de Innovación</h2>
                <h3 class="text-4xl lg:text-5xl font-black text-slate-900 mb-6 tracking-tight">Transformando Datos en Decisiones</h3>
                <p class="text-lg text-slate-500 leading-relaxed">El Modelo AgileQ++ integra tecnologías de vanguardia con rigor académico para garantizar la calidad en cada proceso universitario.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- Card 1 --}}
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:border-upds-blue/20 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-upds-blue text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="bi bi-trophy-fill"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-800 mb-3">Acreditación Continua</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Gestión sistemática de estándares para procesos de autoevaluación institucional.</p>
                </div>

                {{-- Card 2 --}}
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:border-upds-blue/20 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-yellow-50 rounded-2xl flex items-center justify-center text-yellow-600 text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="bi bi-bar-chart-steps"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-800 mb-3">Indicadores 3G</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Métricas avanzadas de tercera generación que miden impacto y pertinencia académica.</p>
                </div>

                {{-- Card 3 --}}
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:border-upds-blue/20 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="bi bi-robot"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-800 mb-3">IA Académica</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Asistencia inteligente para el análisis de datos y seguimiento docente en tiempo real.</p>
                </div>

                {{-- Card 4 --}}
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl hover:border-upds-blue/20 transition-all duration-300 group">
                    <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 text-2xl mb-6 group-hover:scale-110 transition-transform">
                        <i class="bi bi-eye-fill"></i>
                    </div>
                    <h4 class="text-lg font-bold text-slate-800 mb-3">Transparencia Total</h4>
                    <p class="text-sm text-slate-500 leading-relaxed">Portal unificado de evidencias para procesos de auditoría externa y acreditación.</p>
                </div>

                {{-- Stats Grandes --}}
                <div class="md:col-span-4 bg-upds-blue rounded-[2.5rem] p-12 flex flex-col justify-center relative overflow-hidden group shadow-2xl mt-8">
                    <div class="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full blur-3xl -mr-32 -mt-32"></div>
                    <div class="absolute bottom-0 left-0 w-64 h-64 bg-upds-gold/10 rounded-full blur-3xl -ml-20 -mb-20"></div>
                    
                    <div class="relative z-10 flex flex-col md:flex-row justify-around items-center text-center gap-12">
                        <div class="space-y-2">
                            <p class="text-6xl lg:text-7xl font-black text-upds-gold tracking-tighter">98%</p>
                            <p class="text-blue-100 font-bold text-xs uppercase tracking-[0.3em]">Nivel de Cumplimiento</p>
                        </div>
                        <div class="hidden md:block w-px h-24 bg-white/10"></div>
                        <div class="space-y-2">
                            <p class="text-6xl lg:text-7xl font-black text-upds-gold tracking-tighter">150+</p>
                            <p class="text-blue-100 font-bold text-xs uppercase tracking-[0.3em]">Indicadores Activos</p>
                        </div>
                        <div class="hidden md:block w-px h-24 bg-white/10"></div>
                        <div class="space-y-2">
                            <p class="text-6xl lg:text-7xl font-black text-upds-gold tracking-tighter">2026</p>
                            <p class="text-blue-100 font-bold text-xs uppercase tracking-[0.3em]">Visión Estratégica</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- COMPROMISO INSTITUCIONAL --}}
    <section id="acreditacion" class="py-24 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                
                {{-- Imagen Institucional --}}
                <div class="relative h-[550px] rounded-[2rem] overflow-hidden shadow-2xl group">
                    <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=1000&auto=format&fit=crop" 
                         alt="Campus UPDS" 
                         class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-upds-blue/90 via-upds-blue/20 to-transparent"></div>
                    
                    <div class="absolute bottom-10 left-10 right-10">
                        <div class="bg-white/10 backdrop-blur-xl border border-white/20 p-8 rounded-3xl">
                            <p class="text-white font-medium text-xl mb-4 leading-relaxed">"La calidad no es un acto, es un hábito que define nuestra excelencia académica."</p>
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-1bg-upds-gold"></div>
                                <p class="text-upds-gold text-xs font-black uppercase tracking-widest">Vicerrectorado Académico UPDS</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Texto Descriptivo --}}
                <div class="space-y-10">
                    <div>
                        <span class="text-upds-blue font-black text-xs uppercase tracking-[0.3em] mb-4 block">Compromiso Institucional</span>
                        <h2 class="text-4xl lg:text-5xl font-black text-slate-900 mb-6 leading-[1.1]">Garantía de Calidad Respaldada</h2>
                        <p class="text-lg text-slate-600 leading-relaxed">
                            La Universidad Privada Domingo Savio impulsa el Modelo AgileQ++ como el estándar dorado para la gestión universitaria moderna en Bolivia y la región.
                        </p>
                    </div>

                    <ul class="space-y-6">
                        @foreach([
                            'Evaluación por pares internacionales y agencias acreditadoras.',
                            'Sincronización de datos en tiempo real con SQL Server Azure.',
                            'Protocolos de seguridad de datos de nivel gubernamental.',
                            'Soporte estratégico continuo a Decanatos y Direcciones.'
                        ] as $feature)
                        <li class="flex items-start gap-4 group">
                            <div class="mt-1 w-6 h-6 rounded-full bg-green-100 flex items-center justify-center text-green-600 shrink-0 group-hover:bg-green-600 group-hover:text-white transition-colors">
                                <i class="bi bi-check-lg text-sm font-bold"></i>
                            </div>
                            <span class="text-slate-700 font-semibold text-base">{{ $feature }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-slate-900 text-slate-300 pt-20 pb-10 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                
                {{-- Columna Info --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center text-upds-gold">
                            <i class="bi bi-mortarboard-fill text-xl"></i>
                        </div>
                        <div>
                            <span class="block text-white font-black text-xl leading-none">AgileQ++</span>
                            <span class="text-[9px] uppercase tracking-widest text-slate-500 font-bold">Inteligencia Académica</span>
                        </div>
                    </div>
                    <p class="text-sm leading-relaxed text-slate-400">
                        Plataforma de inteligencia académica para la gestión estratégica y acreditación de la calidad educativa en la UPDS.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-9 h-9 rounded-xl bg-slate-800 flex items-center justify-center hover:bg-upds-gold hover:text-upds-blue transition-all"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="w-9 h-9 rounded-xl bg-slate-800 flex items-center justify-center hover:bg-upds-gold hover:text-upds-blue transition-all"><i class="bi bi-linkedin"></i></a>
                        <a href="#" class="w-9 h-9 rounded-xl bg-slate-800 flex items-center justify-center hover:bg-upds-gold hover:text-upds-blue transition-all"><i class="bi bi-envelope-at-fill"></i></a>
                    </div>
                </div>

                {{-- Columna Enlaces --}}
                <div>
                    <h4 class="text-white font-bold mb-8 text-xs uppercase tracking-[0.2em]">Plataforma</h4>
                    <ul class="space-y-4 text-sm font-medium">
                        <li><a href="#" class="hover:text-upds-gold transition-colors">Panel de Indicadores</a></li>
                        <li><a href="#" class="hover:text-upds-gold transition-colors">Procesos de Acreditación</a></li>
                        <li><a href="#" class="hover:text-upds-gold transition-colors">Reportes de Gestión</a></li>
                        <li><a href="#" class="hover:text-upds-gold transition-colors">Seguimiento de Calidad</a></li>
                    </ul>
                </div>

                {{-- Columna Soporte --}}
                <div>
                    <h4 class="text-white font-bold mb-8 text-xs uppercase tracking-[0.2em]">Soporte</h4>
                    <ul class="space-y-4 text-sm font-medium">
                        <li><a href="#" class="hover:text-upds-gold transition-colors">Manual de Usuario</a></li>
                        <li><a href="#" class="hover:text-upds-gold transition-colors">Capacitación Docente</a></li>
                        <li><a href="#" class="hover:text-upds-gold transition-colors">Mesa de Ayuda</a></li>
                        <li><a href="#" class="hover:text-upds-gold transition-colors">Seguridad de Datos</a></li>
                    </ul>
                </div>

                {{-- Columna Contacto --}}
                <div>
                    <h4 class="text-white font-bold mb-8 text-xs uppercase tracking-[0.2em]">Sede Central</h4>
                    <ul class="space-y-4 text-sm font-medium">
                        <li class="flex items-start gap-3">
                            <i class="bi bi-geo-alt-fill text-upds-gold text-base"></i>
                            <span class="text-slate-400">Av. Beni y 3er Anillo Interno<br>Santa Cruz, Bolivia</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="bi bi-telephone-fill text-upds-gold"></i>
                            <span class="text-slate-400">+591 3 3421234</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="bi bi-globe text-upds-gold"></i>
                            <span class="text-slate-400">www.upds.edu.bo</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-6 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                <p>© 2026 Universidad Privada Domingo Savio. Todos los derechos reservados.</p>
                <div class="flex gap-8">
                    <a href="#" class="hover:text-white transition-colors">Privacidad</a>
                    <a href="#" class="hover:text-white transition-colors">Términos</a>
                    <a href="#" class="hover:text-white transition-colors">Acreditación</a>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>