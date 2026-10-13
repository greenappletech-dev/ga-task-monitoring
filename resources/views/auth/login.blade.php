<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'Project Tracker') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

    <style>
        :root {
            --primary: #8BC53F;
            --primary-dark: #1B7540;
            --gradient-start: #8BC53F;
            --gradient-end: #2D8B50;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        .login-container {
            display: flex;
            height: 100vh;
        }

        .login-left {
            flex: 1;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .login-brand {
            position: relative;
            z-index: 1;
            text-align: center;
            color: white;
        }

        .login-brand-logo {
            width: 100%;
            max-width: 400px;
            margin: 0 auto 2rem;
            display: block;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.15));
        }

        .login-brand h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .login-brand p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 400px;
            margin: 0 auto;
        }

        .login-right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #f8f9fa;
        }

        .login-form-container {
            width: 100%;
            max-width: 450px;
            background: white;
            padding: 3rem;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .login-form-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .login-form-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1a1d23;
            margin-bottom: 0.5rem;
        }

        .login-form-header p {
            color: #6c757d;
            font-size: 0.95rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 2px solid #e2e5ea;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(139, 197, 63, 0.1);
        }

        .form-floating > label {
            padding: 1rem;
        }

        .password-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .password-wrapper .form-floating {
            margin-bottom: 0;
        }

        .btn-toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 0.5rem;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-toggle-password:hover {
            color: var(--primary);
        }

        .btn-toggle-password i {
            font-size: 1.1rem;
        }

        .password-wrapper .form-control {
            padding-right: 3rem;
        }

        .form-check {
            margin-bottom: 1.5rem;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            margin-bottom: 1rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(139, 197, 63, 0.4);
        }

        .forgot-password {
            text-align: center;
            display: block;
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .forgot-password:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .login-left {
                display: none;
            }

            .login-form-container {
                padding: 2rem 1.5rem;
            }

            .login-right {
                padding: 1rem;
            }
        }

        @media (max-width: 576px) {
            body {
                overflow-y: auto;
                height: auto;
            }

            .login-container {
                height: auto;
                min-height: 100vh;
            }

            .login-form-container {
                padding: 1.5rem 1rem;
                box-shadow: none;
            }

            .login-form-header h2 {
                font-size: 1.5rem;
            }

            .btn-login {
                padding: 0.75rem;
                font-size: 0.95rem;
            }

            .form-control {
                font-size: 0.9rem;
            }
        }

        .features-list {
            margin-top: 3rem;
            text-align: left;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            color: white;
        }

        .feature-item i {
            margin-right: 1rem;
            font-size: 1.5rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Branding -->
        <div class="login-left">
            <div class="login-brand">
                <img src="{{ asset('images/logo.png') }}" alt="Green Apple Technologies" class="login-brand-logo">
                <p>Streamline your project management with powerful task tracking and team collaboration</p>

                <div class="features-list">
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Kanban board with drag & drop</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Role-based access control</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Real-time task management</span>
                    </div>
                    <div class="feature-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Comprehensive reporting</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="login-right">
            <div class="login-form-container">
                <div class="login-form-header">
                    <h2>Welcome Back</h2>
                    <p>Please sign in to your account</p>
                </div>

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-floating mb-3">
                        <input id="email" type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="name@example.com"
                               required autocomplete="email" autofocus>
                        <label for="email"><i class="bi bi-envelope me-2"></i>Email Address</label>
                        @error('email')
                            <div class="invalid-feedback">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror
                    </div>

                    <div class="password-wrapper">
                        <div class="form-floating">
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password"
                                   placeholder="Password"
                                   required autocomplete="current-password">
                            <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                        </div>
                        <button class="btn-toggle-password" type="button" id="togglePassword">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                        @error('password')
                            <div class="text-danger mt-1">
                                <small><strong>{{ $message }}</strong></small>
                            </div>
                        @enderror
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Remember me for 30 days
                        </label>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                    </button>

                    @if (Route::has('password.request'))
                        <a class="forgot-password" href="{{ route('password.request') }}">
                            Forgot your password?
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('togglePasswordIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('bi-eye');
                passwordIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('bi-eye-slash');
                passwordIcon.classList.add('bi-eye');
            }
        });
    </script>
</body>
</html>
