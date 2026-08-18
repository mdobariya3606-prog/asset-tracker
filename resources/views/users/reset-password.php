<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password — AssetTracker</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/css/form.css">
</head>

<body>
    <div class="register-container">
        <div class="card">

            <div class="card-header">
                <h1>Reset User Password</h1>

                <p>
                    Set a new password for
                    <strong><?= htmlspecialchars($user['name']) ?></strong>.
                    The old password is not required.
                </p>
            </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert-error">
                    <i data-lucide="circle-alert"></i>

                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form
                method="post"
                action="index.php?route=users/reset-password&id=<?= (int)$user['id'] ?>"
                id="resetPasswordForm"
                novalidate>

                <div class="form-grid">

                    <!-- New Password -->
                    <div class="form-group full-width <?= isset($errors['password']) ? 'has-error' : '' ?>">

                        <label for="password">
                            New Password <span class="required">*</span>
                        </label>

                        <div class="input-wrapper">

                            <input
                                type="password"
                                name="password"
                                id="password"
                                placeholder="8-30 chars, 1 upper, 1 lower, 1 number, 1 symbol"
                                autofocus>

                            <i data-lucide="lock" class="input-icon"></i>

                            <button
                                type="button"
                                class="pass-toggle"
                                onclick="togglePassword('password', this)">
                                <i data-lucide="eye" class="eye-open"></i>
                                <i data-lucide="eye-off" class="eye-closed" style="display:none"></i>
                            </button>

                        </div>

                        <?php if (isset($errors['password'])): ?>
                            <div class="error-text">
                                <i data-lucide="circle-alert"></i>
                                <?= htmlspecialchars($errors['password']) ?>
                            </div>
                        <?php endif; ?>

                    </div>


                    <!-- Confirm Password -->
                    <div class="form-group full-width <?= isset($errors['password_confirmation']) ? 'has-error' : '' ?>">

                        <label for="password_confirmation">
                            Confirm Password <span class="required">*</span>
                        </label>

                        <div class="input-wrapper">

                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                placeholder="Re-enter password">

                            <svg class="input-icon" viewBox="0 0 24 24">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>

                            <button
                                type="button"
                                class="pass-toggle"
                                onclick="togglePassword('password_confirmation', this)">
                                <i data-lucide="eye" class="eye-open"></i>
                                <i data-lucide="eye-off" class="eye-closed" style="display:none"></i>
                            </button>

                        </div>

                        <?php if (isset($errors['password_confirmation'])): ?>
                            <div class="error-text">
                                <i data-lucide="circle-alert"></i>

                                <?= htmlspecialchars($errors['password_confirmation']) ?>
                            </div>
                        <?php endif; ?>

                    </div>


                    <!-- Actions -->
                    <div class="full-width reset-actions">

                        <a
                            href="index.php?route=users"
                            class="btn-secondary">
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn-submit"
                            id="submitBtn">
                            Reset Password
                        </button>

                    </div>

                </div>

            </form>

            <div class="form-footer">
                <span>
                    Resetting password for
                    <strong><?= htmlspecialchars($user['name']) ?></strong>
                </span>
            </div>

        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>

    <script>
        // ---------------------------------------------------------
        // Password visibility toggle
        // ---------------------------------------------------------

        function togglePassword(fieldId, btn) {

            const input = document.getElementById(fieldId);
            const eyeOpen = btn.querySelector('.eye-open');
            const eyeClosed = btn.querySelector('.eye-closed');

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

            const form = document.getElementById('resetPasswordForm');

            if (!form) {
                return;
            }

            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            const submitBtn = document.getElementById('submitBtn');


            // ---------------------------------------------------------
            // UI Error Helper
            // ---------------------------------------------------------

            const showError = (input, message) => {

                const formGroup = input.closest('.form-group');

                if (!formGroup) {
                    return;
                }

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


            // ---------------------------------------------------------
            // UI Clear Error Helper
            // ---------------------------------------------------------

            const clearError = (input) => {

                const formGroup = input.closest('.form-group');

                if (!formGroup) {
                    return;
                }

                formGroup.classList.remove('has-error');

                const errorEl = formGroup.querySelector('.error-text');

                if (errorEl) {
                    errorEl.remove();
                }
            };


            // ---------------------------------------------------------
            // Required Validation
            // ---------------------------------------------------------

            const validateRequired = (input, fieldName) => {

                if (!input.value.trim()) {

                    showError(
                        input,
                        `${fieldName} is required.`
                    );

                    return false;
                }

                clearError(input);

                return true;
            };


            // ---------------------------------------------------------
            // Password Validation
            // ---------------------------------------------------------

            const validatePassword = (input) => {

                if (!validateRequired(input, 'New Password')) {
                    return false;
                }

                const val = input.value;

                if (val.length < 8 || val.length > 30) {
                    showError(input, 'Password must be 8–30 characters long.');
                    return false;
                }

                if (!/[A-Z]/.test(val)) {
                    showError(input, 'Password must contain at least 1 uppercase letter.');
                    return false;
                }

                if (!/[a-z]/.test(val)) {
                    showError(input, 'Password must contain at least 1 lowercase letter.');
                    return false;
                }

                if (!/[0-9]/.test(val)) {
                    showError(input, 'Password must contain at least 1 number.');
                    return false;
                }

                if (!/[^A-Za-z0-9]/.test(val)) {
                    showError(input, 'Password must contain at least 1 symbol.');
                    return false;
                }

                clearError(input);

                if (confirmPasswordInput.value) {
                    validateConfirmPassword(confirmPasswordInput);
                }

                return true;
            };


            // ---------------------------------------------------------
            // Confirm Password Validation
            // ---------------------------------------------------------

            const validateConfirmPassword = (input) => {

                if (!validateRequired(input, 'Confirm Password')) {
                    return false;
                }

                if (input.value !== passwordInput.value) {

                    showError(
                        input,
                        'Passwords do not match.'
                    );

                    return false;
                }

                clearError(input);

                return true;
            };


            // ---------------------------------------------------------
            // Real-time Validation
            // ---------------------------------------------------------

            passwordInput.addEventListener(
                'input',
                () => validatePassword(passwordInput)
            );

            passwordInput.addEventListener(
                'blur',
                () => validatePassword(passwordInput)
            );

            confirmPasswordInput.addEventListener(
                'input',
                () => validateConfirmPassword(confirmPasswordInput)
            );

            confirmPasswordInput.addEventListener(
                'blur',
                () => validateConfirmPassword(confirmPasswordInput)
            );


            // ---------------------------------------------------------
            // Form Submit
            // ---------------------------------------------------------

            form.addEventListener('submit', (e) => {

                let isValid = true;

                if (!validatePassword(passwordInput)) {
                    isValid = false;
                }

                if (!validateConfirmPassword(confirmPasswordInput)) {
                    isValid = false;
                }


                if (!isValid) {

                    e.preventDefault();

                    const firstError = form.querySelector('.has-error');

                    if (firstError) {

                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });

                        const input = firstError.querySelector('input');

                        if (input) {
                            input.focus();
                        }
                    }

                    return;
                }


                // Prevent double submission
                submitBtn.disabled = true;

                submitBtn.innerHTML = `
                <svg
                    viewBox="0 0 24 24"
                    style="
                        animation: spin .8s linear infinite;
                        width: 18px;
                        height: 18px;
                        stroke: currentColor;
                        fill: none;
                        stroke-width: 2;
                        stroke-linecap: round;
                        stroke-linejoin: round;
                    "
                >
                    <path d="M12 2v4"/>
                    <path d="M12 18v4"/>
                    <path d="M4.93 4.93l2.83 2.83"/>
                    <path d="M16.24 16.24l2.83 2.83"/>
                    <path d="M2 12h4"/>
                    <path d="M18 12h4"/>
                    <path d="M4.93 19.07l2.83-2.83"/>
                    <path d="M16.24 7.76l2.83-2.83"/>
                </svg>
                Resetting...
            `;
            });

        });
    </script>
</body>

</html>