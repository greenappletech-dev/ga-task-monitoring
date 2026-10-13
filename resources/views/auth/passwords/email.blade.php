<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Project Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #8BC53F;
            --primary-dark: #2D8B50;
            --primary-light: #A8D96E;
            --gradient-start: #8BC53F;
            --gradient-end: #2D8B50;
        }

        body {
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .forgot-password-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }

        .forgot-password-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }

        .forgot-password-logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 1rem;
        }

        .forgot-password-body {
            padding: 2rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(139, 197, 63, 0.4);
        }

        .back-to-login {
            text-align: center;
            margin-top: 1.5rem;
        }

        .back-to-login a {
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 500;
        }

        .back-to-login a:hover {
            color: var(--primary);
        }

        @media (max-width: 576px) {
            .forgot-password-header {
                padding: 1.5rem;
            }

            .forgot-password-body {
                padding: 1.5rem;
            }

            .forgot-password-logo {
                max-width: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <div class="forgot-password-header">
            <img src="{{ asset('images/logo.png') }}" alt="Green Apple Technologies" class="forgot-password-logo">
            <h4 class="mb-0">Reset Your Password</h4>
        </div>

        <div class="forgot-password-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            <p class="text-muted mb-4">Enter your email address and we'll send you a link to reset your password.</p>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                               placeholder="your.email@example.com">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-send"></i> Send Password Reset Link
                </button>
            </form>

            <div class="back-to-login">
                <a href="{{ route('login') }}">
                    <i class="bi bi-arrow-left"></i> Back to Login
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
