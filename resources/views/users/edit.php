<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/form.css">
    <style>
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
            <div class="card-header">
                <div class="icon">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="../storage/profile_images/<?= htmlspecialchars($user['profile_image']) ?>"
                            alt="Profile image">
                    <?php else: ?>
                        <svg viewBox="0 0 24 24">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z" />
                        </svg>
                    <?php endif; ?>
                </div>
                <h1>Edit User Profile</h1>
                <p>Modify details for <?php echo htmlspecialchars($old['name'] ?? $user['name'] ?? 'User'); ?></p>
            </div>

            <!-- ========================= -->
            <!-- General Validation Error / Top Alert -->
            <!-- ========================= -->
            <?php if (!empty($errors['general'])): ?>
                <div class="alert-error">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>

            <!-- ========================= -->
            <!-- Top Success Alert (Global) -->
            <!-- ========================= -->
            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert-success" style="display: flex; align-items: center; gap: 8px; background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.875rem;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    <?php
                    echo htmlspecialchars($_SESSION['success']);
                    unset($_SESSION['success']); // Clear session variable after display
                    ?>
                </div>
            <?php endif; ?>

            <form action="index.php?route=users/edit&id=<?= $user_id ?> ?>" method="post"
                enctype="multipart/form-data" novalidate id="editForm">
                <div class="form-grid">
                    <!-- Name -->
                    <div class="form-group full-width <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                        <label for="name">Full Name <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="name" id="name" placeholder="Enter full name"
                                value="<?php echo htmlspecialchars($old['name'] ?? $user['name'] ?? ''); ?>">
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
                                </svg><?php echo htmlspecialchars($errors['name']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="form-group full-width <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="email" name="email" id="email" placeholder="name@company.com"
                                value="<?php echo htmlspecialchars($old['email'] ?? $user['email'] ?? ''); ?>">
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
                                </svg><?php echo htmlspecialchars($errors['email']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Mobile -->
                    <div class="form-group <?php echo isset($errors['mobile']) ? 'has-error' : ''; ?>">
                        <label for="mobile">Mobile <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="mobile" id="mobile" placeholder="10-digit number" maxlength="10"
                                value="<?php echo htmlspecialchars($old['mobile'] ?? $user['mobile'] ?? ''); ?>">
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
                                </svg><?php echo htmlspecialchars($errors['mobile']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php
                    $viewerSessionRole = strtoupper($_SESSION['user_role'] ?? 'EMPLOYEE');
                    $isOwnProfile = (int)($user['id'] ?? 0) === (int)($_SESSION['user_id'] ?? 0);
                    $targetRole = strtoupper($old['role'] ?? $user['role'] ?? 'EMPLOYEE');
                    $canEditRole = $viewerSessionRole === 'ADMIN';
                    $canEditDepartment = $viewerSessionRole === 'ADMIN' || (!$isOwnProfile && $viewerSessionRole !== 'EMPLOYEE');
                    $canEditDesignation = $viewerSessionRole === 'ADMIN' || ($viewerSessionRole === 'MANAGER' && !$isOwnProfile && $targetRole !== 'ADMIN');
                    ?>

                    <?php if ($canEditRole): ?>
                        <!-- Role -->
                        <div class="form-group <?php echo isset($errors['role']) ? 'has-error' : ''; ?>">
                            <label for="role">Role <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <select name="role" id="role">
                                    <option value="EMPLOYEE" <?php echo ($targetRole === 'EMPLOYEE') ? 'selected' : ''; ?>>
                                        Employee
                                    </option>
                                    <option value="MANAGER" <?php echo ($targetRole === 'MANAGER') ? 'selected' : ''; ?>>
                                        Manager
                                    </option>
                                    <option value="HR" <?php echo ($targetRole === 'HR') ? 'selected' : ''; ?>>HR</option>
                                    <?php if ($viewerSessionRole === 'ADMIN'): ?>
                                        <option value="ADMIN" <?php echo ($targetRole === 'ADMIN') ? 'selected' : ''; ?>>
                                            Admin
                                        </option>
                                    <?php endif; ?>
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
                                    </svg><?php echo htmlspecialchars($errors['role']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($canEditDepartment): ?>
                        <!-- Department -->
                        <div class="form-group <?php echo isset($errors['department_id']) ? 'has-error' : ''; ?>">
                            <label for="department_id">Department <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <select name="department_id" id="department_id">
                                    <option value="">Select department</option>
                                    <?php $currentDept = $old['department_id'] ?? $user['department_id'] ?? ''; ?>
                                    <?php foreach ($departments ?? [] as $department): ?>
                                        <option value="<?php echo $department['id']; ?>" <?php echo ($currentDept == $department['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($department['name']); ?></option>
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
                                    </svg><?php echo htmlspecialchars($errors['department_id']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($canEditDesignation): ?>
                        <!-- Designation -->
                        <div class="form-group <?php echo isset($errors['designation_id']) ? 'has-error' : ''; ?>">
                            <label for="designation_id">Designation <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <select name="designation_id" id="designation_id">
                                    <option value="">Select designation</option>
                                    <?php $currentDesig = $old['designation_id'] ?? $user['designation_id'] ?? ''; ?>
                                    <?php foreach ($designations ?? [] as $designation): ?>
                                        <option value="<?php echo $designation['id']; ?>" <?php echo ($currentDesig == $designation['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($designation['name']); ?></option>
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
                                    </svg><?php echo htmlspecialchars($errors['designation_id']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-group full-width <?php echo isset($errors['profile_image']) ? 'has-error' : ''; ?>">
                        <label for="profile_image">Profile Image (optional)</label>
                        <div class="input-wrapper">
                            <input type="file" name="profile_image" id="profile_image" accept=".png,.jpg,.jpeg,.webp">
                        </div>
                        <?php if (!empty($user['profile_image'])): ?>
                            <div class="error-text current-img-notice"
                                style="margin-top: 10px; display: block; color: #475569;">
                                Current image: <strong><?php echo htmlspecialchars($user['profile_image']); ?></strong>
                            </div>
                        <?php endif; ?>
                        <?php if (isset($errors['profile_image'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg><?php echo htmlspecialchars($errors['profile_image']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Submit & Cancel -->
                    <div class="actions-row">
                        <a href="index.php?route=users" class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-submit">
                            <span class="btn-content">
                                <svg viewBox="0 0 24 24">
                                    <path
                                        d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                    <polyline
                                        points="17 21 17 13 7 13 7 21" />
                                    <polyline points="7 3 7 8 15 8" />
                                </svg>
                                Save Changes
                            </span>
                        </button>
                    </div>

                    <div class="forgot-password-notice">
                        Forgot password? <a href="index.php?route=send-rp-mail" class="reset-link">Send mail</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('editForm');
            if (!form) return;

            // Helper: Show Error Message
            const showError = (input, message) => {
                const formGroup = input.closest('.form-group');
                if (!formGroup) return;

                formGroup.classList.add('has-error');
                let errorEl = formGroup.querySelector('.error-text:not(.current-img-notice)');

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

            // Helper: Clear Error Message
            const clearError = (input) => {
                const formGroup = input.closest('.form-group');
                if (!formGroup) return;

                formGroup.classList.remove('has-error');
                const errorEl = formGroup.querySelector('.error-text:not(.current-img-notice)');
                if (errorEl) {
                    errorEl.remove();
                }
            };

            // --- Validation Rules ---

            // 1. Required Field Validation
            const validateRequired = (input, fieldName) => {
                if (!input.value.trim()) {
                    showError(input, `${fieldName} is required.`);
                    return false;
                }
                clearError(input);
                return true;
            };

            // 2. Email Validation
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

            // 3. Mobile Number Validation (Numeric & 10 digits)
            const validateMobile = (input) => {
                // Enforce numeric only input strictly on typing
                input.value = input.value.replace(/\D/g, '');

                if (!validateRequired(input, 'Mobile number')) return false;
                if (input.value.length !== 10) {
                    showError(input, 'Mobile number must be exactly 10 digits.');
                    return false;
                }
                clearError(input);
                return true;
            };

            // 4. Password Strength Validation (Minimum 6 Characters)
            const validatePassword = (input) => {
                if (!input.value) {
                    clearError(input);
                    return true; // Optional if editing profile without changing password
                }
                if (input.value.length < 6) {
                    showError(input, 'Password must be at least 6 characters long.');
                    return false;
                }
                clearError(input);
                return true;
            };

            // 5. Image & File Size Validation (.jpg, .jpeg, .png, .webp & Max 2MB)
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
                    input.value = ''; // Reset invalid file choice
                    return false;
                }

                if (file.size > maxSizeInBytes) {
                    showError(input, 'File size must not exceed 2 MB.');
                    input.value = ''; // Reset invalid file choice
                    return false;
                }

                clearError(input);
                return true;
            };

            // --- Dynamic Event Listeners ---
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

            const passwordInput = document.getElementById('password');
            if (passwordInput) {
                passwordInput.addEventListener('input', () => validatePassword(passwordInput));
                passwordInput.addEventListener('blur', () => validatePassword(passwordInput));
            }

            const roleInput = document.getElementById('role');
            if (roleInput) {
                roleInput.addEventListener('change', () => validateRequired(roleInput, 'Role'));
            }

            const departmentInput = document.getElementById('department_id');
            if (departmentInput) {
                departmentInput.addEventListener('change', () => validateRequired(departmentInput, 'Department'));
            }

            const designationInput = document.getElementById('designation_id');
            if (designationInput) {
                designationInput.addEventListener('change', () => validateRequired(designationInput, 'Designation'));
            }

            const imageInput = document.getElementById('profile_image');
            if (imageInput) {
                imageInput.addEventListener('change', () => validateProfileImage(imageInput));
            }

            // --- Form Submission Guard ---
            form.addEventListener('submit', (e) => {
                let isValid = true;

                if (nameInput && !validateRequired(nameInput, 'Full Name')) isValid = false;
                if (emailInput && !validateEmail(emailInput)) isValid = false;
                if (mobileInput && !validateMobile(mobileInput)) isValid = false;
                if (passwordInput && !validatePassword(passwordInput)) isValid = false;
                if (roleInput && !validateRequired(roleInput, 'Role')) isValid = false;
                if (departmentInput && !validateRequired(departmentInput, 'Department')) isValid = false;
                if (designationInput && !validateRequired(designationInput, 'Designation')) isValid = false;
                if (imageInput && !validateProfileImage(imageInput)) isValid = false;

                if (!isValid) {
                    e.preventDefault();
                    // Scroll to the first error element smoothly
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
</body>

</html>