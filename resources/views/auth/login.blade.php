@extends('layouts.app')

@section('content')
{{-- Estilos y recursos centralizados para consistencia visual --}}
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    :root {
        --upds-blue: #003566;
        --upds-gold: #ffc300;
        --slate-50: #f8fafc;
    }
    
    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--slate-50);
    }

    .login-bg-pattern {
        background-image: radial-gradient(#003566 0.5px, transparent 0.5px), radial-gradient(#003566 0.5px, #f8fafc 0.5px);
        background-size: 20px 20px;
        background-position: 0 0, 10px 10px;
        opacity: 0.05;
    }

    .login-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 25px 50px -12px rgba(0, 53, 102, 0.15);
    }

    .modern-input {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background-color: #f1f5f9;
        border: 2px solid transparent;
    }

    .modern-input:focus {
        background-color: #ffffff;
        border-color: var(--upds-blue);
        box-shadow: 0 0 0 4px rgba(0, 53, 102, 0.05);
        outline: none;
    }

    .btn-login {
        background-image: linear-gradient(135deg, var(--upds-blue) 0%, #001d36 100%);
        transition: all 0.4s ease;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 20px -5px rgba(0, 53, 102, 0.4);
    }
</style>

<div class="fixed inset-0 w-full h-full flex items-center justify-center p-4">
    <div class="absolute inset-0 login-bg-pattern z-0 pointer-events-none"></div>

    <div class="login-card w-full max-w-[420px] rounded-[2.5rem] overflow-hidden relative z-10">
        
        <div class="text-center pt-12 pb-6 px-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-blue-50 text-[#003566] mb-6 shadow-sm border border-blue-100/50">
                <i class="bi bi-mortarboard-fill text-4xl"></i>
            </div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">SIA <span class="text-[#003566]">UPDS</span></h2>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mt-2">Modelo AgileQ++ | v4.0</p>
        </div>

        <div class="px-10 pb-12">
            {{-- Manejo de Errores de Autenticación --}}
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-2xl animate-pulse">
                    <div class="flex items-center">
                        <i class="bi bi-exclamation-octagon-fill text-red-500 mr-2"></i>
                        <p class="text-xs font-bold text-red-700">{{ $errors->first() }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Credencial (Se mapea a la columna 'Correo' en el controlador) --}}
                <div>
                    <label for="email" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">
                        Credencial Institucional
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="bi bi-envelope-at-fill text-slate-400 group-focus-within:text-[#003566] transition-colors"></i>
                        </div>
                        <input type="email" name="email" id="email"
                               class="modern-input w-full pl-11 pr-4 py-4 rounded-2xl text-sm font-semibold text-slate-700 placeholder-slate-300" 
                               placeholder="usuario@upds.net.bo" 
                               value="{{ old('email') }}" required autofocus>
                    </div>
                </div>

                {{-- Contraseña (Se mapea a la columna 'Contraseña' en el modelo Usuario) --}}
                <div>
                    <label for="password" class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-2 ml-1">
                        Contraseña de Acceso
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="bi bi-shield-lock-fill text-slate-400 group-focus-within:text-[#003566] transition-colors"></i>
                        </div>
                        <input type="password" name="password" id="passwordInput"
                               class="modern-input w-full pl-11 pr-12 py-4 rounded-2xl text-sm font-semibold text-slate-700 placeholder-slate-300" 
                               placeholder="••••••••" required>
                        
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600">
                            <i class="bi bi-eye-fill" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between px-1 py-2">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-[#003566] focus:ring-[#003566] transition-all">
                        <span class="ml-2 text-xs font-bold text-slate-500 group-hover:text-slate-700">Mantener sesión</span>
                    </label>
                    <a href="#" class="text-xs font-bold text-[#003566] hover:text-[#ffc300] transition-colors">¿Ayuda?</a>
                </div>

                <button type="submit" class="btn-login w-full py-4 rounded-2xl text-white font-black text-xs tracking-[0.2em] shadow-xl group">
                    <span class="flex items-center justify-center gap-3">
                        INGRESAR AL SISTEMA <i class="bi bi-arrow-right-circle-fill text-lg group-hover:translate-x-1 transition-transform"></i>
                    </span>
                </button>
            </form>
        </div>

        <div class="bg-slate-50/80 border-t border-slate-100 p-6 text-center">
            <div class="flex items-center justify-center gap-4 grayscale opacity-40 hover:grayscale-0 hover:opacity-100 transition-all duration-500">
                <img src="https://www.upds.edu.bo/wp-content/uploads/2025/02/26-02-1-1.jpg" alt="CIEES" class="h-6">
                <div class="w-px h-4 bg-slate-300"></div>
                <img src="https://th.bing.com/th/id/R.d911541c12c23fc91ee8d2095929fa16?..." alt="MERCOSUR" class="h-6">
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('toggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye-fill', 'bi-eye-slash-fill');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye-slash-fill', 'bi-eye-fill');
        }
    }
</script>
@endsection