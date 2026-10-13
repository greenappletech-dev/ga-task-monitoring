<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Project Tracker</title>
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

        .reset-password-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }

        .reset-password-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }

        .reset-password-logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 1rem;
        }

        .reset-password-body {
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

        .password-wrapper {
            position: relative;
        }

        .btn-toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #666;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            z-index: 10;
        }

        .btn-toggle-password:hover {
            color: var(--primary-dark);
        }

        @media (max-width: 576px) {
            .reset-password-header {
                padding: 1.5rem;
            }

            .reset-password-body {
                padding: 1.5rem;
            }

            .reset-password-logo {
                max-width: 120px;
            }
        }
    </style>
</head>
<body>
    <div class="reset-password-container">
        <div class="reset-password-header">
            <img src="{{ asset('images/logo.png') }}" alt="Green Apple Technologies" class="reset-password-logo">
            <h4 class="mb-0">Set New Password</h4>
        </div>

        <div class="reset-password-body">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                               placeholder="your.email@example.com">
                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <div class="password-wrapper">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock"></i>
                            </span>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                   name="password" required autocomplete="new-password"
                                   placeholder="Enter your new password">
                        </div>
                        <button type="button" class="btn-toggle-password" onclick="togglePassword('password', this)">
                            <i class="bi bi-eye" id="password-icon"></i>
                        </button>
                        @error('password')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <small class="text-muted">Minimum 8 characters</small>
                </div>

                <div class="mb-4">
                    <label for="password-confirm" class="form-label">Confirm New Password</label>
                    <div class="password-wrapper">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-lock-fill"></i>
                            </span>
                            <input id="password-confirm" type="password" class="form-control"
                                   name="password_confirmation" required autocomplete="new-password"
                                   placeholder="Confirm your new password">
                        </div>
                        <button type="button" class="btn-toggle-password" onclick="togglePassword('password-confirm', this)">
                            <i class="bi bi-eye" id="password-confirm-icon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-shield-check"></i> Reset Password
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword(fieldId, button) {
            const field = document.getElementById(fieldId);
            const icon = button.querySelector('i');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
</body>
</html>
