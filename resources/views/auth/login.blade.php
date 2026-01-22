<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso SIA - UPDS</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        /* =========================================================
           SIA - ESTILOS DE AUTENTICACIÓN V4.0
           ========================================================= */
        :root {
            --upds-blue: #003566;
            --upds-blue-dark: #001d3d;
            --upds-blue-light: #00509d;
            --upds-gold: #ffc300;
            --upds-gold-hover: #ffb700;
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        /* 1. FONDO CORPORATIVO */
        .sia-auth-bg {
            background: radial-gradient(circle at top right, var(--upds-blue-light), var(--upds-blue-dark));
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        /* Patrón de fondo sutil (opcional) */
        .sia-auth-bg::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: 0;
        }

        /* 2. TARJETA DE LOGIN */
        .sia-auth-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            background-color: #ffffff;
            overflow: hidden;
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 10;
        }

        /* 3. ENCABEZADO */
        .sia-card-header {
            background-color: var(--upds-blue);
            padding: 3rem 2rem 2rem;
            text-align: center;
            position: relative;
            border-bottom: 5px solid var(--upds-gold);
        }

        /* 4. INPUTS */
        .form-floating > .form-control {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            height: 58px;
            font-weight: 500;
            color: var(--upds-blue);
        }

        .form-floating > .form-control:focus {
            border-color: var(--upds-blue-light);
            box-shadow: 0 0 0 4px rgba(0, 53, 102, 0.1);
        }

        .form-floating > label {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        .input-group-text {
            border-radius: 0 12px 12px 0;
            border: 2px solid #e2e8f0;
            border-left: none;
            background: white;
        }
        
        /* Input Password Ajuste */
        .password-field {
            border-radius: 12px 0 0 12px !important;
            border-right: none !important;
        }
        .password-field:focus {
            z-index: 1;
        }

        /* 5. BOTÓN PRINCIPAL */
        .btn-sia-login {
            background-color: var(--upds-blue);
            color: white;
            font-weight: 800;
            font-size: 1rem;
            padding: 14px;
            border-radius: 50px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border: none;
            box-shadow: 0 10px 20px rgba(0, 53, 102, 0.2);
            transition: all 0.3s ease;
        }

        .btn-sia-login:hover {
            background-color: var(--upds-blue-light);
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(0, 53, 102, 0.3);
            color: white;
        }

        /* 6. UTILIDADES */
        .text-upds-gold { color: var(--upds-gold); }
        .text-upds-blue { color: var(--upds-blue); }
    </style>
</head>
<body>

    <div class="sia-auth-bg">
        <div class="container d-flex justify-content-center px-4">
            
            <div class="card sia-auth-card">
                
                {{-- Encabezado Visual UPDS --}}
                <div class="sia-card-header">
                    <div class="mb-3">
                        <i class="bi bi-mortarboard-fill text-upds-gold display-3 drop-shadow"></i>
                    </div>
                    <h4 class="fw-black text-white mb-0 tracking-tight">SIA - UPDS</h4>
                    <p class="text-white-50 small text-uppercase tracking-widest mb-0 font-monospace">Sistema de Acreditación</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    
                    <div class="text-center mb-4">
                        <h6 class="fw-bold text-upds-blue">Bienvenido</h6>
                        <p class="text-muted small">Ingrese sus credenciales institucionales para continuar.</p>
                    </div>

                    <form action="{{ route('login') }}" method="POST">
                        @csrf

                        {{-- Email --}}
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="floatingInput" placeholder="nombre@upds.edu.bo" value="{{ old('email') }}" required autofocus>
                            <label for="floatingInput">Correo Institucional</label>
                            @error('email')
                                <div class="invalid-feedback ps-2 fw-bold small">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Password con Toggle --}}
                        <div class="input-group mb-4">
                            <div class="form-floating flex-grow-1">
                                <input type="password" name="password" class="form-control password-field @error('password') is-invalid @enderror" id="passwordInput" placeholder="Contraseña" required>
                                <label for="passwordInput">Contraseña</label>
                            </div>
                            <span class="input-group-text px-3" style="cursor: pointer;" onclick="togglePassword()">
                                <i class="bi bi-eye text-muted fs-5" id="toggleIcon"></i>
                            </span>
                            @error('password')
                                <div class="invalid-feedback d-block ps-2 fw-bold small">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Recordarme y Olvidé Contraseña --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="rememberCheck" style="cursor: pointer;">
                                <label class="form-check-label small text-secondary fw-semibold" for="rememberCheck" style="cursor: pointer;">
                                    Recordarme
                                </label>
                            </div>
                            </div>

                        {{-- Botón CTA --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-sia-login">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Acceder al Portal
                            </button>
                        </div>

                    </form>
                </div>
                
                {{-- Footer Login --}}
                <div class="bg-gray-50 text-center py-3 border-top border-light">
                    <p class="text-muted text-[10px] small mb-0 uppercase tracking-wider font-bold" style="font-size: 0.7rem;">
                        UPDS Santa Cruz &copy; {{ date('Y') }}
                    </p>
                </div>
            </div>

        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('passwordInput');
            const icon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
                icon.classList.add('text-upds-blue'); // Highlight color when visible
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.remove('text-upds-blue');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>