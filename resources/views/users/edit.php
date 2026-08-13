<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/form.css">
    <style>
        /* Avatar & Media Controls Styling */
        .avatar-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .avatar-wrapper {
            position: relative;
            width: 110px;
            height: 110px;
            border-radius: 50%;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
            border: 3px solid #ffffff;
            outline: 2px solid #e2e8f0;
            background-color: #f8fafc;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .avatar-wrapper:hover {
            transform: scale(1.03);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18);
        }

        .avatar-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            background-color: #f1f5f9;
        }

        .avatar-placeholder svg {
            width: 48px;
            height: 48px;
            stroke: currentColor;
            fill: none;
            stroke-width: 1.5;
        }

        .avatar-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .avatar-wrapper:hover .avatar-overlay {
            opacity: 1;
        }

        .avatar-overlay svg {
            width: 24px;
            height: 24px;
            stroke: #ffffff;
            fill: none;
            stroke-width: 2;
        }

        /* Image Lightbox Modal */
        .modal-lightbox {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 9999;
            background-color: rgba(15, 23, 42, 0.88);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.25s ease forwards;
        }

        .modal-lightbox.active {
            display: flex;
        }

        .modal-lightbox-content {
            position: relative;
            max-width: 90vw;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .modal-lightbox-img {
            max-width: 80vw;
            max-height: 65vh;
            border-radius: 60px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
            object-fit: contain;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        /* Avatar Action Buttons inside Fullscreen Modal */
        .modal-avatar-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
            width: 100%;
        }

        .btn-avatar-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.25);
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            backdrop-filter: blur(4px);
        }

        .btn-avatar-action:hover {
            background-color: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.4);
            color: #ffffff;
        }

        .btn-avatar-action.btn-danger {
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.4);
            background-color: rgba(239, 68, 68, 0.15);
        }

        .btn-avatar-action.btn-danger:hover {
            background-color: rgba(239, 68, 68, 0.35);
            border-color: rgba(239, 68, 68, 0.6);
            color: #ffffff;
        }

        .btn-avatar-action svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }

        .modal-close-btn {
            position: absolute;
            top: -45px;
            right: 0;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: #ffffff;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .modal-close-btn:hover {
            background: rgba(255, 255, 255, 0.4);
        }

        .modal-close-btn svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }

        #modalUploadBtnText,
        #btnModalRemoveImage {
            font-family: Inter;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="edit-container<?php echo (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN') ? ' admin-edit' : ''; ?>">
        <div class="card">
            <div class="card-header">
                <!-- Profile Avatar Circle Header -->
                <div class="avatar-section">
                    <div class="avatar-wrapper" id="avatarWrapper" title="<?php echo !empty($user['profile_image']) ? 'Click to view profile photo' : 'Upload photo'; ?>">
                        <?php if (!empty($user['profile_image'])): ?>
                            <img src="../storage/profile_images/<?= htmlspecialchars($user['profile_image']) ?>" id="avatarImage" alt="Profile image">
                            <div class="avatar-overlay">
                                <svg viewBox="0 0 24 24">
                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                            </div>
                        <?php else: ?>
                            <div class="avatar-placeholder" id="avatarPlaceholder">
                                <svg viewBox="0 0 24 24">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <h1>Edit User Profile</h1>
                <p>Modify details for <?php echo htmlspecialchars($old['name'] ?? $user['name'] ?? 'User'); ?></p>
            </div>

            <!-- Validation Error Alert -->
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

            <!-- Success Alert -->
            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert-success" style="display: flex; align-items: center; gap: 8px; background-color: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; font-size: 0.875rem;">
                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12" />
                    </svg>
                    <?php
                    echo htmlspecialchars($_SESSION['success']);
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <form action="index.php?route=users/edit&id=<?= $user_id ?>" method="post" enctype="multipart/form-data" novalidate id="editForm">
                <!-- Hidden file and delete status inputs -->
                <input type="file" name="profile_image" id="profile_image" accept=".png,.jpg,.jpeg,.webp" style="display: none;">
                <input type="hidden" name="delete_profile_image" id="delete_profile_image" value="0">

                <div class="form-grid">
                    <!-- Name -->
                    <div class="form-group full-width <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                        <label for="name">Full Name <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="name" id="name" placeholder="Enter full name" value="<?php echo htmlspecialchars($old['name'] ?? $user['name'] ?? ''); ?>">
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
                            <input type="email" name="email" id="email" placeholder="name@company.com" value="<?php echo htmlspecialchars($old['email'] ?? $user['email'] ?? ''); ?>">
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
                            <input type="text" name="mobile" id="mobile" placeholder="10-digit number" maxlength="10" value="<?php echo htmlspecialchars($old['mobile'] ?? $user['mobile'] ?? ''); ?>">
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
                                    <option value="EMPLOYEE" <?php echo ($targetRole === 'EMPLOYEE') ? 'selected' : ''; ?>>Employee</option>
                                    <option value="MANAGER" <?php echo ($targetRole === 'MANAGER') ? 'selected' : ''; ?>>Manager</option>
                                    <option value="HR" <?php echo ($targetRole === 'HR') ? 'selected' : ''; ?>>HR</option>
                                    <?php if ($viewerSessionRole === 'ADMIN'): ?>
                                        <option value="ADMIN" <?php echo ($targetRole === 'ADMIN') ? 'selected' : ''; ?>>Admin</option>
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

                    <!-- Form Actions -->
                    <div class="actions-row">
                        <a href="index.php?route=users" class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-submit">
                            <span class="btn-content">
                                <svg viewBox="0 0 24 24">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                    <polyline points="17 21 17 13 7 13 7 21" />
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

    <!-- Fullscreen Lightbox Modal with Interactive Action Buttons -->
    <div class="modal-lightbox" id="imageModal" aria-hidden="true">
        <div class="modal-lightbox-content">
            <button type="button" class="modal-close-btn" id="modalCloseBtn" aria-label="Close picture viewer">
                <svg viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18" />
                    <line x1="6" y1="6" x2="18" y2="18" />
                </svg>
            </button>

            <!-- Enlarged Image -->
            <img src="" id="modalPreviewImg" class="modal-lightbox-img" alt="Profile Preview">

            <!-- Options Placed Directly Below Image inside Lightbox -->
            <div class="modal-avatar-actions">
                <button type="button" class="btn-avatar-action" id="btnModalTriggerUpload">
                    <svg viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="17 8 12 3 7 8" />
                        <line x1="12" y1="3" x2="12" y2="15" />
                    </svg>
                    <span id="modalUploadBtnText"><?php echo !empty($user['profile_image']) ? 'Change Photo' : 'Upload Photo'; ?></span>
                </button>

                <button type="button" class="btn-avatar-action btn-danger" id="btnModalRemoveImage" style="<?php echo empty($user['profile_image']) ? 'display: none;' : ''; ?>">
                    <svg viewBox="0 0 24 24">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                    </svg>
                    Delete Photo
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('editForm');
            if (!form) return;

            // Media Elements & Control Nodes
            const avatarWrapper = document.getElementById('avatarWrapper');
            const imageInput = document.getElementById('profile_image');
            const deleteInput = document.getElementById('delete_profile_image');

            // Lightbox Modal Elements
            const imageModal = document.getElementById('imageModal');
            const modalPreviewImg = document.getElementById('modalPreviewImg');
            const modalCloseBtn = document.getElementById('modalCloseBtn');
            const btnModalTriggerUpload = document.getElementById('btnModalTriggerUpload');
            const btnModalRemoveImage = document.getElementById('btnModalRemoveImage');
            const modalUploadBtnText = document.getElementById('modalUploadBtnText');

            // Helper: Show Error Message
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

            // Helper: Clear Error Message
            const clearError = (input) => {
                const formGroup = input.closest('.form-group');
                if (!formGroup) return;

                formGroup.classList.remove('has-error');
                const errorEl = formGroup.querySelector('.error-text');
                if (errorEl) {
                    errorEl.remove();
                }
            };

            // --- Lightbox Modal Logic ---
            const openModal = (src) => {
                if (!src) return;
                modalPreviewImg.src = src;
                imageModal.classList.add('active');
                imageModal.setAttribute('aria-hidden', 'false');
            };

            const closeModal = () => {
                imageModal.classList.remove('active');
                imageModal.setAttribute('aria-hidden', 'true');
            };

            if (avatarWrapper) {
                avatarWrapper.addEventListener('click', () => {
                    const img = avatarWrapper.querySelector('img');
                    if (img && img.src) {
                        openModal(img.src);
                    } else {
                        imageInput.click();
                    }
                });
            }

            if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeModal);
            if (imageModal) {
                imageModal.addEventListener('click', (e) => {
                    if (e.target === imageModal) closeModal();
                });
            }

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && imageModal.classList.contains('active')) {
                    closeModal();
                }
            });

            // --- Handlers inside Modal ---
            if (btnModalTriggerUpload) {
                btnModalTriggerUpload.addEventListener('click', () => {
                    imageInput.click();
                });
            }

            if (btnModalRemoveImage) {
                btnModalRemoveImage.addEventListener('click', () => {
                    if (confirm('Are you sure you want to remove your profile picture?')) {
                        deleteInput.value = '1';
                        imageInput.value = '';

                        // Revert Avatar placeholder on form
                        avatarWrapper.innerHTML = `
                            <div class="avatar-placeholder" id="avatarPlaceholder">
                                <svg viewBox="0 0 24 24">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                            </div>
                        `;
                        avatarWrapper.title = "Upload photo";

                        closeModal();
                        if (modalUploadBtnText) modalUploadBtnText.textContent = 'Upload Photo';
                        btnModalRemoveImage.style.display = 'none';
                    }
                });
            }

            // --- Validation & Image Upload Handling ---
            const validateRequired = (input, fieldName) => {
                if (!input.value.trim()) {
                    showError(input, `${fieldName} is required.`);
                    return false;
                }
                clearError(input);
                return true;
            };

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

            const validateMobile = (input) => {
                input.value = input.value.replace(/\D/g, '');
                if (!validateRequired(input, 'Mobile number')) return false;
                if (input.value.length !== 10) {
                    showError(input, 'Mobile number must be exactly 10 digits.');
                    return false;
                }
                clearError(input);
                return true;
            };

            const validateProfileImage = (input) => {
                if (!input.files || input.files.length === 0) {
                    return true;
                }

                const file = input.files[0];
                const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
                const fileExtension = file.name.split('.').pop().toLowerCase();
                const maxSizeInBytes = 2 * 1024 * 1024; // 2 MB

                if (!allowedExtensions.includes(fileExtension)) {
                    alert('Only .jpg, .jpeg, .png, and .webp files are allowed.');
                    input.value = '';
                    return false;
                }

                if (file.size > maxSizeInBytes) {
                    alert('File size must not exceed 2 MB.');
                    input.value = '';
                    return false;
                }

                // Render Live Preview on form and in Modal
                const reader = new FileReader();
                reader.onload = (e) => {
                    avatarWrapper.innerHTML = `
                        <img src="${e.target.result}" id="avatarImage" alt="Profile image">
                        <div class="avatar-overlay">
                            <svg viewBox="0 0 24 24">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </div>
                    `;
                    avatarWrapper.title = "Click to view profile photo";
                    deleteInput.value = '0';

                    // Update controls in modal
                    if (modalUploadBtnText) modalUploadBtnText.textContent = 'Change Photo';
                    if (btnModalRemoveImage) btnModalRemoveImage.style.display = 'inline-flex';

                    // Automatically open full view with the new photo
                    openModal(e.target.result);
                };
                reader.readAsDataURL(file);

                return true;
            };

            // --- Event Listeners ---
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

            if (imageInput) {
                imageInput.addEventListener('change', () => validateProfileImage(imageInput));
            }

            // --- Form Submit Guard ---
            form.addEventListener('submit', (e) => {
                let isValid = true;

                if (nameInput && !validateRequired(nameInput, 'Full Name')) isValid = false;
                if (emailInput && !validateEmail(emailInput)) isValid = false;
                if (mobileInput && !validateMobile(mobileInput)) isValid = false;
                if (roleInput && !validateRequired(roleInput, 'Role')) isValid = false;
                if (departmentInput && !validateRequired(departmentInput, 'Department')) isValid = false;
                if (designationInput && !validateRequired(designationInput, 'Designation')) isValid = false;
                if (imageInput && !validateProfileImage(imageInput)) isValid = false;

                if (!isValid) {
                    e.preventDefault();
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