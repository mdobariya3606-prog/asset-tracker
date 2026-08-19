<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Department</title>
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

        .input-icon {
            width: 18px;
            height: 18px;
            color: #64748b;
            stroke-width: 1.8;
        }
    </style>
</head>

<body>
    <div class="edit-container<?php

                                use App\helpers\Csrf;

                                echo (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN') ? ' admin-edit' : ''; ?>">
        <div class="card">
            <!-- ========================= -->
            <!-- Card Header -->
            <!-- ========================= -->
            <div class="card-header">
                <div class="icon">
                    <i data-lucide="building-2"></i>
                </div>
                <h1>Add New Department</h1>
            </div>

            <!-- ========================= -->
            <!-- General Validation Error -->
            <!-- ========================= -->
            <?php if (!empty($errors['general'])): ?>
                <div class="alert-error">
                    <i data-lucide="circle-alert"></i>
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>

            <!-- ========================= -->
            <!-- Add Department Form -->
            <!-- ========================= -->
            <form action="index.php?route=departments/create" method="post" id="addDepartmentForm" novalidate>
                <div class="form-grid">

                    <?= Csrf::field() ?>

                    <!-- ========================= -->
                    <!-- Name Field -->
                    <!-- ========================= -->
                    <div class="form-group full-width <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                        <label for="name">Department Name <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="name" id="name" placeholder="ex. Finance"
                                value="<?php echo htmlspecialchars($old['name'] ?? $assetData['name'] ?? ''); ?>"
                                autofocus>
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 7 12 3 4 7v10l8 4 8-4V7z" />
                                <path d="M12 21V11" />
                                <path d="M4 7l8 4 8-4" />
                            </svg>
                        </div>
                        <?php if (isset($errors['name'])): ?>
                            <div class="error-text">
                                <i data-lucide="circle-alert"></i>
                                <?php echo htmlspecialchars($errors['name']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Form Actions -->
                    <!-- Cancel & Save Buttons -->
                    <!-- ========================= -->
                    <div class="actions-row">
                        <a href="index.php?route=departments" class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span class="btn-content">
                                <i data-lucide="file-text"></i>
                                <span>Add Department</span>
                            </span>
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('addDepartmentForm');
            if (!form) return;

            const nameInput = document.getElementById('name');
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

            const validateName = () => {
                const val = nameInput.value.trim();
                if (!val) {
                    showError(nameInput, 'Department Name is required.');
                    return false;
                }
                if (val.length < 2) {
                    showError(nameInput, 'Department Name must be at least 2 characters.');
                    return false;
                }
                clearError(nameInput);
                return true;
            };

            nameInput.addEventListener('input', validateName);
            nameInput.addEventListener('blur', validateName);

            form.addEventListener('submit', (e) => {
                if (!validateName()) {
                    e.preventDefault();
                    nameInput.focus();
                    return;
                }

                submitBtn.disabled = true;
                const btnContent = submitBtn.querySelector('.btn-content');
                if (btnContent) {
                    btnContent.innerHTML = `
                    <svg class="spinner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                    </svg>
                    <span>Saving...</span>
                `;
                }
            });
        });
    </script>
</body>

</html>