<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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

        /* Success message styling under input */
        .success-text {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8125rem;
            color: #166534;
            margin-top: 6px;
        }

        .success-text svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
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
                    <!-- Mail Envelope Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                        <polyline points="22,6 12,13 2,6" />
                    </svg>
                </div>
                <h1>Forgot Password</h1>
                <p style="margin-top: 6px; color: #6b7280; font-size: 0.875rem;">Enter your account's email address and we'll send you a password reset link.</p>
            </div>

            <!-- ========================= -->
            <!-- General Validation Error / Top Alert -->
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
            <!-- Forgot Password Form -->
            <!-- ========================= -->
            <form action="" method="post" id="forgotPasswordForm" novalidate>
                <div class="form-grid">
                    <!-- ========================= -->
                    <!-- Email Field -->
                    <!-- ========================= -->
                    <div class="form-group full-width <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="email" name="email" id="email" placeholder="name@company.com" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" autofocus>
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                                <polyline points="22,6 12,13 2,6" />
                            </svg>
                        </div>

                        <!-- Backend Error Message under input -->
                        <?php if (isset($errors['email'])): ?>
                            <div class="error-text" data-backend-msg="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['email']); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Backend Success Message under input -->
                        <?php if (!empty($_SESSION['success'])): ?>
                            <div class="success-text" data-backend-msg="true">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12" />
                                </svg>
                                <?php
                                echo htmlspecialchars($_SESSION['success']);
                                unset($_SESSION['success']); // Clear session variable after display
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Form Actions -->
                    <!-- ========================= -->
                    <div class="actions-row">
                        <a href="index.php?route=login" class="btn-cancel">Back to Login</a>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span class="btn-content">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13" />
                                    <polygon points="22 2 15 22 11 13 2 9 22 2" />
                                </svg>
                                <span>Send Reset Link</span>
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
            const form = document.getElementById('forgotPasswordForm');
            if (!form) return;

            const emailInput = document.getElementById('email');
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

                // Remove success message if present
                const successEl = group.querySelector('.success-text');
                if (successEl) successEl.remove();

                let errorEl = group.querySelector('.error-text');

                if (!errorEl) {
                    errorEl = document.createElement('div');
                    errorEl.className = 'error-text';
                    group.appendChild(errorEl);
                }

                errorEl.removeAttribute('data-backend-msg');
                errorEl.innerHTML = '';
                errorEl.appendChild(createErrorSvg());
                errorEl.appendChild(document.createTextNode(' ' + message));
            };

            const clearError = (input) => {
                const group = input.closest('.form-group');
                if (!group) return;

                const errorEl = group.querySelector('.error-text');

                // Preserve backend error message on blur unless user types
                if (errorEl && errorEl.getAttribute('data-backend-msg') === 'true') {
                    return;
                }

                group.classList.remove('has-error');
                if (errorEl) {
                    errorEl.remove();
                }
            };

            // When user types, clear backend messages (errors & successes)
            emailInput.addEventListener('input', () => {
                const group = emailInput.closest('.form-group');
                if (group) {
                    const backendMsg = group.querySelector('[data-backend-msg="true"]');
                    if (backendMsg) {
                        backendMsg.remove();
                    }
                }
                validateEmail();
            });

            const validateEmail = () => {
                const val = emailInput.value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                if (!val) {
                    showError(emailInput, 'Email Address is required.');
                    return false;
                }
                if (!emailRegex.test(val)) {
                    showError(emailInput, 'Please enter a valid email address.');
                    return false;
                }
                clearError(emailInput);
                return true;
            };

            emailInput.addEventListener('blur', validateEmail);

            form.addEventListener('submit', (e) => {
                // Remove backend messages prior to submit validation
                const group = emailInput.closest('.form-group');
                if (group) {
                    const backendMsg = group.querySelector('[data-backend-msg="true"]');
                    if (backendMsg) backendMsg.remove();
                }

                const isEmailValid = validateEmail();

                if (!isEmailValid) {
                    e.preventDefault();
                    emailInput.focus();
                    return;
                }

                submitBtn.disabled = true;
                const btnContent = submitBtn.querySelector('.btn-content');
                if (btnContent) {
                    btnContent.innerHTML = `
                    <svg class="spinner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                    </svg>
                    <span>Sending Mail...</span>
                `;
                }
            });
        });
    </script>
</body>

</html>