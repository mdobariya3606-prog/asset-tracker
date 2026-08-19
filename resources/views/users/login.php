<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Subtle decorative blobs */
        body::before,
        body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .35;
            z-index: 0;
            pointer-events: none;
        }

        body::before {
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, #3b82f6 0%, transparent 70%);
            top: -120px;
            right: -100px;
        }

        body::after {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, #06b6d4 0%, transparent 70%);
            bottom: -80px;
            left: -60px;
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 44px 40px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }

        .card-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .card-header .icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #3b82f6, #06b6d4);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
        }

        .card-header .icon svg {
            width: 28px;
            height: 28px;
            fill: none;
            stroke: #fff;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .card-header h1 {
            font-size: 26px;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -0.5px;
        }

        .card-header p {
            color: #94a3b8;
            font-size: 14px;
            margin-top: 6px;
        }

        /* Alerts */
        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.4s ease;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #7f1d1d;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.4s ease;
        }

        .alert-success svg,
        .alert-error svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            fill: none;
            stroke-width: 2;
        }

        .alert-success svg {
            stroke: #34d399;
        }

        .alert-error svg {
            stroke: #f87171;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form */
        .form-group {
            position: relative;
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .form-group label .required {
            color: #f87171;
            margin-left: 2px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            stroke: #94a3b8;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: stroke 0.3s;
            pointer-events: none;
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px 12px 44px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            color: #1e293b;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }

        .form-group input::placeholder {
            color: #94a3b8;
        }

        .form-group input:focus {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .form-group input:focus~.input-icon {
            stroke: #3b82f6;
        }

        /* Error state */
        .form-group.has-error input {
            border-color: #f87171;
            background: rgba(248, 113, 113, 0.06);
        }

        .form-group.has-error input:focus {
            box-shadow: 0 0 0 4px rgba(248, 113, 113, 0.15);
        }

        .error-text {
            color: #ef4444;
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
            animation: slideDown 0.3s ease;
        }

        .error-text svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
            stroke: #f87171;
            fill: none;
            stroke-width: 2;
        }

        /* Password toggle */
        .pass-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            display: flex;
        }

        .pass-toggle svg {
            width: 18px;
            height: 18px;
            stroke: #94a3b8;
            fill: none;
            stroke-width: 2;
            transition: stroke 0.2s;
        }

        .pass-toggle:hover svg {
            stroke: #475569;
        }

        /* Remember me */
        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #3b82f6;
            border-radius: 4px;
            cursor: pointer;
        }

        .remember-me span {
            font-size: 13px;
            color: #475569;
            font-weight: 500;
        }

        .forgot-link {
            font-size: 13px;
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #2563eb;
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #133458;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(19, 52, 88, 0.35);
        }

        .btn-submit:hover::before {
            transform: translateX(100%);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit .btn-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit svg {
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 16px;
            margin: 24px 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            color: #94a3b8;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        /* Footer link */
        .form-footer {
            text-align: center;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .form-footer span {
            color: #94a3b8;
            font-size: 13px;
        }

        .form-footer a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .form-footer a:hover {
            color: #2563eb;
        }

        /* Logged-in banner */
        .logged-in-banner {
            text-align: center;
            padding: 24px 0 0;
        }

        .logged-in-banner .avatar {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #3b82f6, #06b6d4);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 4px 16px rgba(59, 130, 246, .25);
        }

        .logged-in-banner .avatar span {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
        }

        .logged-in-banner h2 {
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .logged-in-banner p {
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .btn-outline {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 24px;
            background: #fff;
            color: #ef4444;
            border: 1.5px solid #fecaca;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all .2s;
        }

        .btn-outline:hover {
            background: #fef2f2;
            border-color: #f87171;
        }

        .btn-outline svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }

        .btn-link-primary {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 24px;
            background: #133458;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all .25s;
            box-shadow: 0 2px 10px rgba(19, 52, 88, .3);
        }

        .btn-link-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, .4);
        }

        .btn-link-primary svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        /* Responsive */
        @media (max-width: 500px) {
            .card {
                padding: 32px 24px;
            }

            .card-header h1 {
                font-size: 22px;
            }

            .form-options {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }
        }
    </style>
    <link rel="stylesheet" href="resources/css/form.css">
</head>

<body>
    <div class="login-container">
        <div class="card">

            <?php if (!empty($_SESSION['user_id'])): ?>
                <!-- Already logged in -->
                <div class="card-header">
                    <div class="icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        </svg>
                    </div>
                    <h1>Welcome Back</h1>
                    <p>You are currently logged in</p>
                </div>

                <div class="logged-in-banner">
                    <div class="avatar">
                        <span><?= strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)) ?></span>
                    </div>
                    <h2><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></h2>
                    <p><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></p>

                    <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap;">
                        <a href="index.php?route=users" class="btn-link-primary">
                            <svg viewBox="0 0 24 24">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                            </svg>
                            View Users
                        </a>
                        <form action="index.php?route=logout" method="post">
                            <button type="submit" class="btn-outline">
                                <svg viewBox="0 0 24 24">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                    <polyline points="16 17 21 12 16 7" />
                                    <line x1="21" y1="12" x2="9" y2="12" />
                                </svg>Logout</button>
                        </form>
                    </div>
                </div>

            <?php else: ?>
                <!-- Login form -->
                <div class="card-header">
                    <div class="icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                            <polyline points="10 17 15 12 10 7" />
                            <line x1="15" y1="12" x2="3" y2="12" />
                        </svg>
                    </div>
                    <h1>Welcome Back</h1>
                    <p>Sign in to your AssetTracker account</p>
                </div>

                <?php if (!empty($success)): ?>
                    <div class="alert-success">
                        <svg viewBox="0 0 24 24">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors['general'])): ?>
                    <div class="alert-error">
                        <svg viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        <?= htmlspecialchars($errors['general']) ?>
                    </div>
                <?php endif; ?>

                <form action="index.php?route=login" method="post" novalidate id="loginForm">

                    <!-- Email -->
                    <div class="form-group <?= isset($errors['email']) ? 'has-error' : '' ?>">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="email" name="email" id="email"
                                placeholder="name@company.com"
                                value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                                autofocus>
                            <svg class="input-icon" viewBox="0 0 24 24">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                        </div>
                        <?php if (isset($errors['email'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?= htmlspecialchars($errors['email']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Password -->
                    <div class="form-group <?= isset($errors['password']) ? 'has-error' : '' ?>">
                        <label for="password">Password <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password"
                                placeholder="Enter your password">
                            <svg class="input-icon" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <button type="button" class="pass-toggle" onclick="togglePassword()">
                                <svg viewBox="0 0 24 24" id="eyeOpen">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg viewBox="0 0 24 24" id="eyeClosed" style="display:none">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </button>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?= htmlspecialchars($errors['password']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Options row -->
                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember">
                            <span>Remember me</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-submit">
                        <span class="btn-content">
                            <svg viewBox="0 0 24 24">
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4" />
                                <polyline
                                    points="10 17 15 12 10 7" />
                                <line x1="15" y1="12" x2="3" y2="12" />
                            </svg>
                            Sign In
                        </span>
                    </button>

                    <div class="forgot-password-notice">
                        Forgot password? <a href="index.php?route=send-rp-mail" class="reset-link">Send mail</a>
                    </div>

                </form>
            <?php endif; ?>

        </div>
    </div>

     <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');
            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.style.display = 'none';
                eyeClosed.style.display = 'block';
            } else {
                input.type = 'password';
                eyeOpen.style.display = 'block';
                eyeClosed.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const loginForm = document.getElementById('loginForm');
            if (!loginForm) return;

            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            // UI Error Helper
            const showError = (input, message) => {
                const formGroup = input.closest('.form-group');
                if (!formGroup) return;

                formGroup.classList.add('has-error');
                let errorEl = formGroup.querySelector('.error-text');

                if (!errorEl) {
                    errorEl = document.createElement('div');
                    errorEl.className = 'error-text';
                    formGroup.appendChild(errorEl);
                }

                errorEl.innerHTML = `
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                ${message}
            `;
            };

            // UI Clear Helper
            const clearError = (input) => {
                const formGroup = input.closest('.form-group');
                if (!formGroup) return;

                formGroup.classList.remove('has-error');
                const errorEl = formGroup.querySelector('.error-text');
                if (errorEl) {
                    errorEl.remove();
                }
            };

            // Validation Checks
            const validateEmail = () => {
                const value = emailInput.value.trim();
                if (!value) {
                    showError(emailInput, 'Email Address is required.');
                    return false;
                }
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    showError(emailInput, 'Please enter a valid email address.');
                    return false;
                }
                clearError(emailInput);
                return true;
            };

            const validatePassword = () => {
                const value = passwordInput.value;
                if (!value) {
                    showError(passwordInput, 'Password is required.');
                    return false;
                }
                clearError(passwordInput);
                return true;
            };

            // Real-time Event Listeners
            if (emailInput) {
                emailInput.addEventListener('input', validateEmail);
                emailInput.addEventListener('blur', validateEmail);
            }

            if (passwordInput) {
                passwordInput.addEventListener('input', validatePassword);
                passwordInput.addEventListener('blur', validatePassword);
            }

            // Form Submit Handler
            loginForm.addEventListener('submit', function(e) {
                const isEmailValid = validateEmail();
                const isPasswordValid = validatePassword();

                if (!isEmailValid || !isPasswordValid) {
                    e.preventDefault();
                    // Focus the first invalid input
                    const firstErrorInput = loginForm.querySelector('.has-error input');
                    if (firstErrorInput) {
                        firstErrorInput.focus();
                    }
                    return;
                }

                // Prevent double-submit & show loading spinner if valid
                const btn = this.querySelector('.btn-submit');
                btn.disabled = true;
                btn.style.opacity = '0.7';
                btn.querySelector('.btn-content').innerHTML = `
                <svg viewBox="0 0 24 24" style="animation:spin .8s linear infinite;width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2">
                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                </svg>
                Signing in...
            `;
            });
        });
    </script> 
    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>