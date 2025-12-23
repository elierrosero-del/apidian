<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>APIDIAN - Iniciar Sesión</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #1e293b 100%);
            position: relative;
            overflow: hidden;
        }

        /* Animated background */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.1) 0%, transparent 50%);
            animation: pulse 15s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(10%, 10%) scale(1.1); }
        }

        .container {
            display: flex;
            width: 100%;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* Left Panel - Branding */
        .brand-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 60px;
            color: white;
        }

        .brand-panel .logo {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #f97316, #ea580c);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 32px;
            box-shadow: 0 20px 60px rgba(249, 115, 22, 0.4);
        }

        .brand-panel .logo svg {
            width: 60px;
            height: 60px;
            color: white;
        }

        .brand-panel h1 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #ffffff, #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-panel p {
            font-size: 1.25rem;
            color: #94a3b8;
            text-align: center;
            max-width: 400px;
            line-height: 1.6;
        }

        .brand-panel .features {
            margin-top: 48px;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .brand-panel .feature {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #cbd5e1;
            font-size: 0.95rem;
        }

        .brand-panel .feature-icon {
            width: 32px;
            height: 32px;
            background: rgba(249, 115, 22, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-panel .feature-icon svg {
            width: 18px;
            height: 18px;
            color: #f97316;
        }

        /* Right Panel - Login Form */
        .login-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .login-card {
            background: white;
            border-radius: 24px;
            padding: 48px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.3);
        }

        .login-card h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
        }

        .login-card .subtitle {
            color: #64748b;
            margin-bottom: 32px;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: #f9fafb;
        }

        .form-group input:focus {
            outline: none;
            border-color: #f97316;
            background: white;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
        }

        .form-group input::placeholder {
            color: #9ca3af;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #f97316;
            cursor: pointer;
        }

        .remember-me span {
            font-size: 0.875rem;
            color: #64748b;
        }

        .forgot-link {
            font-size: 0.875rem;
            color: #f97316;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #ea580c;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 20px rgba(249, 115, 22, 0.4);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(249, 115, 22, 0.5);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 24px 0;
            color: #9ca3af;
            font-size: 0.875rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .divider span {
            padding: 0 16px;
        }

        .register-link {
            text-align: center;
            font-size: 0.95rem;
            color: #64748b;
        }

        .register-link a {
            color: #f97316;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .error-message {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 16px;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .brand-panel {
                display: none;
            }
            
            .login-panel {
                flex: 1;
            }
        }

        @media (max-width: 480px) {
            .login-panel {
                padding: 20px;
            }
            
            .login-card {
                padding: 32px 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Brand Panel -->
        <div class="brand-panel">
            <div class="logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h1>APIDIAN</h1>
            <p>Sistema de Facturación Electrónica para Colombia. Conecta tu negocio con la DIAN de forma segura y eficiente.</p>
            
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span>Validación automática con la DIAN</span>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <span>Firma digital certificada</span>
                </div>
                <div class="feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span>API REST de alto rendimiento</span>
                </div>
            </div>
        </div>

        <!-- Login Panel -->
        <div class="login-panel">
            <div class="login-card">
                <h2>Bienvenido</h2>
                <p class="subtitle">Ingresa tus credenciales para continuar</p>

                @if ($errors->has('email'))
                    <div class="error-message">
                        {{ $errors->first('email') }}
                    </div>
                @endif

                @if ($errors->has('password'))
                    <div class="error-message">
                        {{ $errors->first('password') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    {{ csrf_field() }}

                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            placeholder="tu@email.com"
                            required 
                            autofocus
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••"
                            required
                        >
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>Recordarme</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-link">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn-login">
                        Iniciar Sesión
                    </button>
                </form>

                @if(env('ALLOW_PUBLIC_REGISTER', true))
                    <div class="divider">
                        <span>o</span>
                    </div>
                    <p class="register-link">
                        ¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate aquí</a>
                    </p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
