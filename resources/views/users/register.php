<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/css/form.css">
</head>

<body>
    <div class="register-container">
        <div class="card">
            <div class="card-header">
                <div class="icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <line x1="19" y1="8" x2="19" y2="14" />
                        <line x1="22" y1="11" x2="16" y2="11" />
                    </svg>
                </div>
                <h1>Create Account</h1>
                <p>Fill in the details to register a new user</p>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert-success">
                    <svg viewBox="0 0 24 24">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                        <polyline points="22 4 12 14.01 9 11.01" />
                    </svg>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form action="index.php?route=users/create" method="post" enctype="multipart/form-data" novalidate
                id="registerForm">
                <div class="form-grid">
                    <div class="form-group full-width <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                        <label for="name">Full Name <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="name" id="name" placeholder="Enter full name"
                                value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>">
                            <svg class="input-icon" viewBox="0 0 24 24">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </div>
                        <?php if (isset($errors['name'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['name']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group full-width <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="email" name="email" id="email" placeholder="name@company.com"
                                value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
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
                                <?php echo htmlspecialchars($errors['email']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group <?php echo isset($errors['mobile']) ? 'has-error' : ''; ?>">
                        <label for="mobile">Mobile <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="mobile" id="mobile" placeholder="10-digit number"
                                value="<?php echo htmlspecialchars($old['mobile'] ?? ''); ?>" maxlength="10">
                            <svg class="input-icon" viewBox="0 0 24 24">
                                <rect x="5" y="2" width="14" height="20" rx="2" ry="2" />
                                <line x1="12" y1="18" x2="12.01" y2="18" />
                            </svg>
                        </div>
                        <?php if (isset($errors['mobile'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['mobile']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group <?php echo isset($errors['department_id']) ? 'has-error' : ''; ?>">
                        <label for="department_id">Department <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <select name="department_id" id="department_id">
                                <option value="">Select department</option>
                                <?php foreach ($departments ?? [] as $department): ?>
                                    <option value="<?php echo $department['id']; ?>"
                                        <?php echo (isset($old['department_id']) && $old['department_id'] == $department['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($department['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <svg class="input-icon" viewBox="0 0 24 24">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                <polyline points="9 22 9 12 15 12 15 22" />
                            </svg>
                        </div>
                        <?php if (isset($errors['department_id'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['department_id']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group <?php echo isset($errors['designation_id']) ? 'has-error' : ''; ?>">
                        <label for="designation_id">Designation <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <select name="designation_id" id="designation_id">
                                <option value="">Select designation</option>
                                <?php foreach ($designations ?? [] as $designation): ?>
                                    <option value="<?php echo $designation['id']; ?>"
                                        <?php echo (isset($old['designation_id']) && $old['designation_id'] == $designation['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($designation['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <svg class="input-icon" viewBox="0 0 24 24">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2" />
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16" />
                            </svg>
                        </div>
                        <?php if (isset($errors['designation_id'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['designation_id']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group <?php echo isset($errors['role']) ? 'has-error' : ''; ?>">
                        <label for="role">Role <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <select name="role" id="role">
                                <option value="">Select role</option>
                                <?php foreach ($roleOptions ?? [] as $roleOption): ?>
                                    <option value="<?php echo htmlspecialchars($roleOption['value']); ?>"
                                        <?php echo (isset($old['role']) && strtoupper($old['role']) == strtoupper($roleOption['value'])) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($roleOption['label']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <svg class="input-icon" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </div>
                        <?php if (isset($errors['role'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['role']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group <?php echo isset($errors['password']) ? 'has-error' : ''; ?>">
                        <label for="password">Password <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="password" name="password" id="password" placeholder="Min 8 chars, 1 upper, 1 lower, 1 number, 1 symbol">
                            <svg class="input-icon" viewBox="0 0 24 24">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                            <button type="button" class="pass-toggle" onclick="togglePassword('password', this)">
                                <svg viewBox="0 0 24 24" class="eye-open">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg viewBox="0 0 24 24" class="eye-closed" style="display:none">
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
                                <?php echo htmlspecialchars($errors['password']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group <?php echo isset($errors['confirm_password']) ? 'has-error' : ''; ?>">
                        <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="password" name="confirm_password" id="confirm_password"
                                placeholder="Re-enter password">
                            <svg class="input-icon" viewBox="0 0 24 24">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            </svg>
                            <button type="button" class="pass-toggle" onclick="togglePassword('confirm_password', this)">
                                <svg viewBox="0 0 24 24" class="eye-open">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg viewBox="0 0 24 24" class="eye-closed" style="display:none">
                                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                                    <line x1="1" y1="1" x2="23" y2="23" />
                                </svg>
                            </button>
                        </div>
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['confirm_password']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group full-width <?php echo isset($errors['profile_image']) ? 'has-error' : ''; ?>">
                        <label for="profile_image">Profile Image (optional)</label>
                        <div class="input-wrapper">
                            <input type="file" name="profile_image" id="profile_image" accept=".png,.jpg,.jpeg,.webp">
                        </div>
                        <?php if (isset($errors['profile_image'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['profile_image']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="full-width">
                        <button type="submit" class="btn-submit">Create Account</button>
                    </div>
                </div>
            </form>

            <div class="form-footer">
                <span><a href="index.php?route=users">View all users</a></span>
            </div>
        </div>
    </div>

    <script>
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
            const form = document.getElementById('registerForm');
            if (!form) return;

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

            // --- Validation Functions ---

            // 1. Generic Required Check
            const validateRequired = (input, fieldName) => {
                if (!input.value.trim()) {
                    showError(input, `${fieldName} is required.`);
                    return false;
                }
                clearError(input);
                return true;
            };

            // 2. Email Format Check
            const validateEmail = (input) => {
                if (!validateRequired(input, 'Email Address')) return false;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(input.value.trim())) {
                    showError(input, 'Please enter a valid email address.');
                    return false;
                }
                clearError(input);
                return true;
            };

            // 3. Mobile Number Validation (Numeric & 10 Digits)
            const validateMobile = (input) => {
                // Force strict numeric digits live
                input.value = input.value.replace(/\D/g, '');

                if (!validateRequired(input, 'Mobile number')) return false;
                if (input.value.length !== 10) {
                    showError(input, 'Mobile number must be exactly 10 digits.');
                    return false;
                }
                clearError(input);
                return true;
            };

            // 4. Password Strength (min 8 chars, 1 upper, 1 lower, 1 number, 1 symbol)
            const validatePassword = (input) => {
                if (!validateRequired(input, 'Password')) return false;

                const val = input.value;
                const rules = [{
                        test: /^.{8,30}$/,
                        message: 'Password must be 8–30 characters long.'
                    },
                    {
                        test: /[A-Z]/,
                        message: 'Password must contain at least 1 uppercase letter.'
                    },
                    {
                        test: /[a-z]/,
                        message: 'Password must contain at least 1 lowercase letter.'
                    },
                    {
                        test: /[0-9]/,
                        message: 'Password must contain at least 1 number.'
                    },
                    {
                        test: /[^A-Za-z0-9]/,
                        message: 'Password must contain at least 1 symbol.'
                    }
                ];

                for (const rule of rules) {
                    if (!rule.test.test(val)) {
                        showError(input, rule.message);
                        return false;
                    }
                }

                clearError(input);

                const confirmInput = document.getElementById('confirm_password');
                if (confirmInput && confirmInput.value) {
                    validateConfirmPassword(confirmInput);
                }
                return true;
            };

            // 5. Confirm Password Match
            const validateConfirmPassword = (input) => {
                if (!validateRequired(input, 'Confirm Password')) return false;
                const passwordInput = document.getElementById('password');
                if (passwordInput && input.value !== passwordInput.value) {
                    showError(input, 'Passwords do not match.');
                    return false;
                }
                clearError(input);
                return true;
            };

            // 6. Profile Image Extension and Size Check (Max 2MB)
            const validateProfileImage = (input) => {
                if (!input.files || input.files.length === 0) {
                    clearError(input);
                    return true;
                }

                const file = input.files[0];
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                const fileExtension = file.name.split('.').pop().toLowerCase();
                const maxSizeInBytes = 2 * 1024 * 1024; // 2 MB

                if (!allowedExtensions.includes(fileExtension)) {
                    showError(input, 'Only .jpg, .jpeg, .png, and .webp files are allowed.');
                    input.value = '';
                    return false;
                }

                if (file.size > maxSizeInBytes) {
                    showError(input, 'File size must not exceed 2 MB.');
                    input.value = '';
                    return false;
                }

                clearError(input);
                return true;
            };

            // --- Real-time Validation Listeners ---
            const nameInput = document.getElementById('name');
            if (nameInput) {
                nameInput.addEventListener('input', () => validateRequired(nameInput, 'Full Name'));
                nameInput.addEventListener('blur', () => validateRequired(nameInput, 'Full Name'));
            }

            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.addEventListener('input', () => validateEmail(emailInput));
                emailInput.addEventListener('blur', () => validateEmail(emailInput));
            }

            const mobileInput = document.getElementById('mobile');
            if (mobileInput) {
                mobileInput.addEventListener('input', () => validateMobile(mobileInput));
                mobileInput.addEventListener('blur', () => validateMobile(mobileInput));
            }

            const deptInput = document.getElementById('department_id');
            if (deptInput) {
                deptInput.addEventListener('change', () => validateRequired(deptInput, 'Department'));
            }

            const desigInput = document.getElementById('designation_id');
            if (desigInput) {
                desigInput.addEventListener('change', () => validateRequired(desigInput, 'Designation'));
            }

            const roleInput = document.getElementById('role');
            if (roleInput) {
                roleInput.addEventListener('change', () => validateRequired(roleInput, 'Role'));
            }

            const passInput = document.getElementById('password');
            if (passInput) {
                passInput.addEventListener('input', () => validatePassword(passInput));
                passInput.addEventListener('blur', () => validatePassword(passInput));
            }

            const confirmPassInput = document.getElementById('confirm_password');
            if (confirmPassInput) {
                confirmPassInput.addEventListener('input', () => validateConfirmPassword(confirmPassInput));
                confirmPassInput.addEventListener('blur', () => validateConfirmPassword(confirmPassInput));
            }

            const imageInput = document.getElementById('profile_image');
            if (imageInput) {
                imageInput.addEventListener('change', () => validateProfileImage(imageInput));
            }

            // --- Form Submit Handler ---
            form.addEventListener('submit', (e) => {
                let isValid = true;

                if (nameInput && !validateRequired(nameInput, 'Full Name')) isValid = false;
                if (emailInput && !validateEmail(emailInput)) isValid = false;
                if (mobileInput && !validateMobile(mobileInput)) isValid = false;
                if (deptInput && !validateRequired(deptInput, 'Department')) isValid = false;
                if (desigInput && !validateRequired(desigInput, 'Designation')) isValid = false;
                if (roleInput && !validateRequired(roleInput, 'Role')) isValid = false;
                if (passInput && !validatePassword(passInput)) isValid = false;
                if (confirmPassInput && !validateConfirmPassword(confirmPassInput)) isValid = false;
                if (imageInput && !validateProfileImage(imageInput)) isValid = false;

                if (!isValid) {
                    e.preventDefault();
                    // Smoothly scroll to the first invalid field
                    const firstError = form.querySelector('.has-error');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }
                }
            });
        });
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>