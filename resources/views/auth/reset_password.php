<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/css/form.css">
    <style>
        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .spinner-icon {
            animation: spin 0.8s linear infinite;
            width: 18px;
            height: 18px;
        }

        button:disabled {
            opacity: 0.75;
            cursor: not-allowed;
        }

        /* Toggle password visibility styling */
        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password:hover {
            color: #374151;
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
        }
    </style>
</head>

<body>
    <div class="edit-container<?php echo (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN') ? ' admin-edit' : ''; ?>">
        <div class="card">
            <!-- ========================= -->
            <!-- Card Header -->
            <!-- ========================= -->
            <div class="card-header">
                <div class="icon">
                    <!-- Key / Shield Lock Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                </div>
                <h1>Reset Password</h1>
            </div>

            <!-- ========================= -->
            <!-- General Validation Error -->
            <!-- ========================= -->
            <?php if (!empty($errors['general'])): ?>
                <div class="alert-error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>

            <!-- ========================= -->
            <!-- Reset Password Form -->
            <!-- ========================= -->
            <form action="" method="post" id="resetPasswordForm" novalidate>
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">

                <div class="form-grid">
                    <!-- ========================= -->
                    <!-- New Password Field -->
                    <!-- ========================= -->
                    <div class="form-group full-width <?php echo isset($errors['password']) ? 'has-error' : ''; ?>">
                        <label for="password">New Password <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password" placeholder="Enter new password" autofocus>
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['password']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Confirm Password Field -->
                    <!-- ========================= -->
                    <div class="form-group full-width <?php echo isset($errors['confirm_password']) ? 'has-error' : ''; ?>">
                        <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                        </div>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['confirm_password']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Form Actions -->
                    <!-- ========================= -->
                    <div class="actions-row">
                        <a href="index.php?route=users" class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span class="btn-content">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                <span>Update Password</span>
                            </span>
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- ========================= -->
    <!-- JS Validation & Loading State -->
    <!-- ========================= -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('resetPasswordForm');
            if (!form) return;

            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('confirm_password');
            const submitBtn = document.getElementById('submitBtn');

            const createErrorSvg = () => {
                const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                svg.setAttribute('viewBox', '0 0 24 24');
                svg.setAttribute('fill', 'none');
                svg.setAttribute('stroke', 'currentColor');
                svg.setAttribute('stroke-width', '2');
                svg.setAttribute('stroke-linecap', 'round');
                svg.setAttribute('stroke-linejoin', 'round');

                const circle = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                circle.setAttribute('cx', '12');
                circle.setAttribute('cy', '12');
                circle.setAttribute('r', '10');

                const line1 = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line1.setAttribute('x1', '12');
                line1.setAttribute('y1', '8');
                line1.setAttribute('x2', '12');
                line1.setAttribute('y2', '12');

                const line2 = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                line2.setAttribute('x1', '12');
                line2.setAttribute('y1', '16');
                line2.setAttribute('x2', '12.01');
                line2.setAttribute('y2', '16');

                svg.appendChild(circle);
                svg.appendChild(line1);
                svg.appendChild(line2);
                return svg;
            };

            const showError = (input, message) => {
                const group = input.closest('.form-group');
                if (!group) return;

                group.classList.add('has-error');
                let errorEl = group.querySelector('.error-text');

                if (!errorEl) {
                    errorEl = document.createElement('div');
                    errorEl.className = 'error-text';
                    group.appendChild(errorEl);
                }

                errorEl.innerHTML = '';
                errorEl.appendChild(createErrorSvg());
                errorEl.appendChild(document.createTextNode(' ' + message));
            };

            const clearError = (input) => {
                const group = input.closest('.form-group');
                if (!group) return;

                group.classList.remove('has-error');
                const errorEl = group.querySelector('.error-text');
                if (errorEl) {
                    errorEl.remove();
                }
            };

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
                return true;
            };

            const validateConfirmPassword = () => {
                const val = confirmInput.value;
                const passwordVal = passwordInput.value;

                if (!val) {
                    showError(confirmInput, 'Please confirm your password.');
                    return false;
                }
                if (val !== passwordVal) {
                    showError(confirmInput, 'Passwords do not match.');
                    return false;
                }
                clearError(confirmInput);
                return true;
            };

            passwordInput.addEventListener('input', () => {
                validatePassword();
                if (confirmInput.value) validateConfirmPassword();
            });
            passwordInput.addEventListener('blur', validatePassword);

            confirmInput.addEventListener('input', validateConfirmPassword);
            confirmInput.addEventListener('blur', validateConfirmPassword);

            form.addEventListener('submit', (e) => {
                const isPasswordValid = validatePassword();
                const isConfirmValid = validateConfirmPassword();

                if (!isPasswordValid || !isConfirmValid) {
                    e.preventDefault();
                    if (!isPasswordValid) {
                        passwordInput.focus();
                    } else {
                        confirmInput.focus();
                    }
                    return;
                }

                submitBtn.disabled = true;
                const btnContent = submitBtn.querySelector('.btn-content');
                if (btnContent) {
                    btnContent.innerHTML = `
                    <svg class="spinner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                    </svg>
                    <span>Updating...</span>
                `;
                }
            });
        });
    </script>
</body>

</html>