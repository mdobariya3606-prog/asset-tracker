<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background: #f1f5f9;
            font-family: Inter, sans-serif;
            color: #1e293b;
        }

        .card {
            width: min(100%, 480px);
            padding: 36px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(15, 23, 42, .08);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 24px;
        }

        .subtitle {
            margin: 0 0 28px;
            color: #64748b;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #475569;
            font-size: 13px;
            font-weight: 600;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font: inherit;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, .12);
        }

        /* Error states */
        .form-group.has-error input {
            border-color: #dc2626;
            background-color: #fef2f2;
        }

        .form-group.has-error input:focus {
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.12);
        }

        .error {
            margin-top: 6px;
            color: #dc2626;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .alert {
            padding: 12px 14px;
            background: #fef2f2;
            color: #991b1b;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            border: 1px solid #fecaca;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 28px;
        }

        .actions a, .actions button {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            font: 600 14px Inter, sans-serif;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .cancel {
            color: #475569;
            border: 1px solid #cbd5e1;
            background: #fff;
        }

        .cancel:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        button {
            border: 0;
            color: #fff;
            background: #7c3aed;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        button:hover {
            background: #6d28d9;
        }

        button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>
<body>
<main class="card">
    <h1>Reset User Password</h1>
    <p class="subtitle">Set a new password for <?= htmlspecialchars($user['name']) ?>. The old password is not
        required.</p>

    <?php if (!empty($errors['general'])): ?>
        <div class="alert"><?= htmlspecialchars($errors['general']) ?></div>
    <?php endif; ?>

    <form method="post" action="index.php?route=users/reset-password&id=<?= (int)$user['id'] ?>" id="resetPasswordForm"
          novalidate>
        <div class="form-group <?= !empty($errors['password']) ? 'has-error' : '' ?>">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" autofocus placeholder="Min 6 characters">
            <?php if (!empty($errors['password'])): ?>
                <div class="error"><?= htmlspecialchars($errors['password']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group <?= !empty($errors['password_confirmation']) ? 'has-error' : '' ?>">
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation"
                   placeholder="Re-enter new password">
            <?php if (!empty($errors['password_confirmation'])): ?>
                <div class="error"><?= htmlspecialchars($errors['password_confirmation']) ?></div>
            <?php endif; ?>
        </div>

        <div class="actions">
            <a class="cancel" href="index.php?route=users">Cancel</a>
            <button type="submit" id="submitBtn">
                <span class="btn-text">Reset Password</span>
            </button>
        </div>
    </form>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('resetPasswordForm');
        if (!form) return;

        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const submitBtn = document.getElementById('submitBtn');

        // UI Error Helper
        const showError = (input, message) => {
            const group = input.closest('.form-group');
            if (!group) return;

            group.classList.add('has-error');
            let errorEl = group.querySelector('.error');

            if (!errorEl) {
                errorEl = document.createElement('div');
                errorEl.className = 'error';
                group.appendChild(errorEl);
            }

            errorEl.textContent = message;
        };

        // UI Clear Helper
        const clearError = (input) => {
            const group = input.closest('.form-group');
            if (!group) return;

            group.classList.remove('has-error');
            const errorEl = group.querySelector('.error');
            if (errorEl) {
                errorEl.remove();
            }
        };

        // Validation logic
        const validatePassword = () => {
            const val = passwordInput.value;
            if (!val) {
                showError(passwordInput, 'New Password is required.');
                return false;
            }
            if (val.length < 6) {
                showError(passwordInput, 'Password must be at least 6 characters long.');
                return false;
            }
            clearError(passwordInput);

            // Live update password match status if confirmation field has a value
            if (confirmInput.value.length > 0) {
                validateConfirmPassword();
            }
            return true;
        };

        const validateConfirmPassword = () => {
            const val = confirmInput.value;
            if (!val) {
                showError(confirmInput, 'Confirm New Password is required.');
                return false;
            }
            if (val !== passwordInput.value) {
                showError(confirmInput, 'Passwords do not match.');
                return false;
            }
            clearError(confirmInput);
            return true;
        };

        // Event Listeners for real-time validation
        passwordInput.addEventListener('input', validatePassword);
        passwordInput.addEventListener('blur', validatePassword);

        confirmInput.addEventListener('input', validateConfirmPassword);
        confirmInput.addEventListener('blur', validateConfirmPassword);

        // Form submission guard
        form.addEventListener('submit', (e) => {
            const isPasswordValid = validatePassword();
            const isConfirmValid = validateConfirmPassword();

            if (!isPasswordValid || !isConfirmValid) {
                e.preventDefault();
                // Focus first field with error
                const firstError = form.querySelector('.has-error input');
                if (firstError) {
                    firstError.focus();
                }
                return;
            }

            // Visual submit loading feedback
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                    <svg viewBox="0 0 24 24" style="animation:spin .8s linear infinite;width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round">
                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                    </svg>
                    <span>Resetting...</span>
                `;
        });
    });
</script>
</body>
</html>