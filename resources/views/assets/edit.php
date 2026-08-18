<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ========================= -->
    <!-- Page Metadata & Assets -->
    <!-- ========================= -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Asset — AssetTracker</title>
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
    </style>
</head>

<body>

    <!-- ========================= -->
    <!-- Edit User Page -->
    <!-- ========================= -->
    <div class="edit-container<?php echo (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN') ? ' admin-edit' : ''; ?>">
        <div class="card">
            <!-- ========================= -->
            <!-- Card Header -->
            <!-- ========================= -->
            <div class="card-header">
                <div class="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                        <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z" />
                    </svg>
                </div>
                <h1>Edit Asset</h1>
                <p>Modify details for <?php echo htmlspecialchars($old['name'] ?? $assetData['name'] ?? 'Asset'); ?></p>
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
            <!-- Edit User Form -->
            <!-- ========================= -->
            <form action="index.php?route=assets/edit&id=<?= (int)($_GET['id'] ?? 0) ?>" method="post"
                enctype="multipart/form-data" id="editAssetForm" novalidate>
                <div class="form-grid">

                    <!-- ========================= -->
                    <!-- Name Field -->
                    <!-- ========================= -->
                    <div class="form-group <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                        <label for="name">Asset Name <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="name" id="name" placeholder="ex. Dell OptiPlex 7010"
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
                    <!-- Serial Number Field -->
                    <!-- ========================= -->
                    <div class="form-group <?php echo isset($errors['serial_number']) ? 'has-error' : ''; ?>">
                        <label for="serial_number">Serial Number <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" id="serial_number" name="serial_number" placeholder="ex. DOPT7010SN15"
                                value="<?= htmlspecialchars($old['serial_number'] ?? $assetData['serial_number'] ?? '') ?>">

                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 7 12 3 4 7v10l8 4 8-4V7z" />
                                <path d="M12 21V11" />
                                <path d="M4 7l8 4 8-4" />
                            </svg>
                        </div>
                        <?php if (isset($errors['serial_number'])): ?>
                            <div class="error-text">
                                <i data-lucide="circle-alert"></i>
                                <?php echo htmlspecialchars($errors['serial_number']); ?>
                            </div>
                        <?php endif; ?>
                    </div>


                    <!-- ========================= -->
                    <!-- Brand Field -->
                    <!-- ========================= -->
                    <div class="form-group <?php echo isset($errors['brand']) ? 'has-error' : ''; ?>">
                        <label for="brand">Brand <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="brand" id="brand" placeholder="ex. Dell"
                                value="<?php echo htmlspecialchars($old['brand'] ?? $assetData['brand'] ?? ''); ?>">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="6" />
                                <path d="M8.5 14 7 22l5-3 5 3-1.5-8" />
                            </svg>
                        </div>
                        <?php if (isset($errors['brand'])): ?>
                            <div class="error-text">
                                <i data-lucide="circle-alert"></i>
                                <?php echo htmlspecialchars($errors['brand']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Model Field -->
                    <!-- ========================= -->
                    <div class="form-group <?php echo isset($errors['model']) ? 'has-error' : ''; ?>">
                        <label for="model">Model <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="model" id="model" placeholder="ex. OptiPlex 7010"
                                value="<?php echo htmlspecialchars($old['model'] ?? $assetData['model'] ?? ''); ?>">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="7" y="7" width="10" height="10" rx="1" />
                                <path d="M9 1v3M15 1v3M9 20v3M15 20v3" />
                                <path d="M20 9h3M20 15h3M1 9h3M1 15h3" />
                            </svg>
                        </div>
                        <?php if (isset($errors['model'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg><?php echo htmlspecialchars($errors['model']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Category Selection -->
                    <!-- ========================= -->
                    <div class="form-group <?php echo isset($errors['category_id']) ? 'has-error' : ''; ?>">
                        <label for="category_id">Category <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <select id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                <?php $selectedCategory = (int)($assetData['category_id'] ?? 0); ?>
                                <?php foreach ($categories ?? [] as $category): ?>
                                    <option value="<?= (int)$category['id'] ?>" <?= $selectedCategory === (int)$category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z" />
                            </svg>
                        </div>

                        <?php if (isset($errors['category_id'])): ?>
                            <div class="error-text">
                                <i data-lucide="circle-alert"></i><?php echo htmlspecialchars($errors['category_id']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Vendor Selection -->
                    <!-- ========================= -->
                    <div class="form-group <?php echo isset($errors['vendor_id']) ? 'has-error' : ''; ?>">
                        <label for="vendor_id">Vendor <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <select id="vendor_id" name="vendor_id">
                                <option value="">Select Vendor</option>
                                <?php $selectedVendor = (int)($assetData['vendor_id'] ?? 0); ?>
                                <?php foreach ($vendors ?? [] as $vendor): ?>
                                    <option value="<?= (int)$vendor['id'] ?>" <?= $selectedVendor === (int)$vendor['id'] ? 'selected' : '' ?>><?= htmlspecialchars($vendor['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 9l1-5h16l1 5" />
                                <path d="M4 9v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9" />
                                <path d="M9 22V12h6v10" />
                            </svg>
                        </div>
                        <?php if (isset($errors['vendor_id'])): ?>
                            <div class="error-text">
                                <i data-lucide="circle-alert"></i><?php echo htmlspecialchars($errors['vendor_id']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Status Selection -->
                    <!-- ========================= -->

                    <div class="form-group <?php echo isset($errors['status']) ? 'has-error' : ''; ?>">
                        <label for="status">Status <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <select name="status" id="status">
                                <option value="">Select Status</option>
                                <?php $currentStat = $assetData['status'] ?? 'AVAILABLE'; ?>
                                <?php foreach ($statusEnum ?? [] as $status): ?>
                                    <option value="<?php echo strtoupper($status) ?>" <?php echo ($currentStat == strtoupper($status) ? 'selected' : ''); ?>><?php echo htmlspecialchars($status); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <line x1="4" y1="21" x2="4" y2="14" />
                                <line x1="4" y1="10" x2="4" y2="3" />
                                <line x1="12" y1="21" x2="12" y2="12" />
                                <line x1="12" y1="8" x2="12" y2="3" />
                                <line x1="20" y1="21" x2="20" y2="16" />
                                <line x1="20" y1="12" x2="20" y2="3" />
                                <circle cx="4" cy="12" r="2" />
                                <circle cx="12" cy="10" r="2" />
                                <circle cx="20" cy="14" r="2" />
                            </svg>
                        </div>
                        <?php if (isset($errors['status'])): ?>
                            <div class="error-text">
                                <i data-lucide="circle-alert"></i><?php echo htmlspecialchars($errors['status']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Cost Field -->
                    <!-- ========================= -->
                    <div class="form-group <?php echo isset($errors['cost']) ? 'has-error' : ''; ?>">
                        <label for="cost">Cost <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="text" name="cost" id="cost" placeholder="ex. 48000" maxlength="10"
                                value="<?php echo htmlspecialchars($old['cost'] ?? $assetData['cost'] ?? ''); ?>">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.59 13.41L11 3H4v7l9.59 9.59a2 2 0 0 0 2.82 0l4.18-4.18a2 2 0 0 0 0-2.82z" />
                                <circle cx="7.5" cy="7.5" r="1.5" />
                            </svg>
                        </div>
                        <?php if (isset($errors['cost'])): ?>
                            <div class="error-text">
                                <i data-lucide="circle-alert"></i>
                                <?php echo htmlspecialchars($errors['cost']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Purchase Date Field -->
                    <!-- ========================= -->
                    <div class="form-group <?php echo isset($errors['purchase_date']) ? 'has-error' : ''; ?>">
                        <label for="purchase_date">Purchase Date <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="date" id="purchase_date" name="purchase_date"
                                value="<?= htmlspecialchars($old['purchase_date'] ?? $assetData['purchase_date'] ?? '') ?>">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                        </div>
                        <?php if (isset($errors['purchase_date'])): ?>
                            <div class="error-text">
                                <i data-lucide="circle-alert"></i>
                                <?php echo htmlspecialchars($errors['purchase_date']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Warranty Date Field -->
                    <!-- ========================= -->
                    <div class="form-group <?php echo isset($errors['warranty_date']) ? 'has-error' : ''; ?>">
                        <label for="warranty_date">Warranty Date <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <input type="date" id="warranty_date" name="warranty_date"
                                value="<?= htmlspecialchars($old['warranty_date'] ?? $assetData['warranty_date'] ?? '') ?>">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <line x1="16" y1="2" x2="16" y2="6" />
                                <line x1="8" y1="2" x2="8" y2="6" />
                                <line x1="3" y1="10" x2="21" y2="10" />
                            </svg>
                        </div>
                        <?php if (isset($errors['warranty_date'])): ?>
                            <div class="error-text">
                                <i data-lucide="circle-alert"></i>
                                <?php echo htmlspecialchars($errors['warranty_date']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Form Actions -->
                    <!-- Cancel & Save Buttons -->
                    <!-- ========================= -->
                    <div class="actions-row">
                        <a href="index.php?route=assets/show&id=<?= (int)($_GET['id'] ?? 0); ?>"
                            class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span class="btn-content">
                                <i data-lucide="file-text"></i>
                                <span>Save Changes</span>
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('editAssetForm');
            if (!form) return;

            const inputs = {
                name: document.getElementById('name'),
                serial_number: document.getElementById('serial_number'),
                brand: document.getElementById('brand'),
                model: document.getElementById('model'),
                category_id: document.getElementById('category_id'),
                vendor_id: document.getElementById('vendor_id'),
                status: document.getElementById('status'),
                cost: document.getElementById('cost'),
                purchase_date: document.getElementById('purchase_date'),
                warranty_date: document.getElementById('warranty_date'),
                image: document.getElementById('image')
            };

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

            // Text input validation
            const validateText = (input, fieldName, minLen = 2) => {
                const val = input.value.trim();
                if (!val) {
                    showError(input, `${fieldName} is required.`);
                    return false;
                }
                if (val.length < minLen) {
                    showError(input, `${fieldName} must be at least ${minLen} characters.`);
                    return false;
                }
                clearError(input);
                return true;
            };

            // Select dropdown validation
            const validateSelect = (select, fieldName) => {
                if (!select.value) {
                    showError(select, `Please select a ${fieldName}.`);
                    return false;
                }
                clearError(select);
                return true;
            };

            // Cost validation
            const validateCost = () => {
                const val = inputs.cost.value.trim();
                if (!val) {
                    showError(inputs.cost, 'Cost is required.');
                    return false;
                }
                if (isNaN(val) || Number(val) <= 0) {
                    showError(inputs.cost, 'Cost must be a positive number.');
                    return false;
                }
                clearError(inputs.cost);
                return true;
            };

            // Date validation
            const validateDates = () => {
                let valid = true;
                if (!inputs.purchase_date.value) {
                    showError(inputs.purchase_date, 'Purchase date is required.');
                    valid = false;
                } else {
                    clearError(inputs.purchase_date);
                }

                if (!inputs.warranty_date.value) {
                    showError(inputs.warranty_date, 'Warranty date is required.');
                    valid = false;
                } else {
                    clearError(inputs.warranty_date);
                }

                if (inputs.purchase_date.value && inputs.warranty_date.value) {
                    const purchase = new Date(inputs.purchase_date.value);
                    const warranty = new Date(inputs.warranty_date.value);
                    if (warranty < purchase) {
                        showError(inputs.warranty_date, 'Warranty date cannot be before purchase date.');
                        valid = false;
                    }
                }

                return valid;
            };

            // Image file validation
            const validateImage = () => {
                const file = inputs.image.files[0];
                if (file) {
                    const allowedTypes = ['image/png', 'image/jpeg', 'image/jpg', 'image/webp'];
                    if (!allowedTypes.includes(file.type)) {
                        showError(inputs.image, 'Allowed formats: PNG, JPG, JPEG, WEBP.');
                        return false;
                    }
                    if (file.size > 5 * 1024 * 1024) { // 5MB limit
                        showError(inputs.image, 'Image size must not exceed 5MB.');
                        return false;
                    }
                }
                clearError(inputs.image);
                return true;
            };

            // Attach event listeners
            inputs.name.addEventListener('input', () => validateText(inputs.name, 'Asset Name'));
            inputs.name.addEventListener('blur', () => validateText(inputs.name, 'Asset Name'));

            inputs.serial_number.addEventListener('input', () => validateText(inputs.serial_number, 'Serial Number'));
            inputs.serial_number.addEventListener('blur', () => validateText(inputs.serial_number, 'Serial Number'));

            inputs.brand.addEventListener('input', () => validateText(inputs.brand, 'Brand'));
            inputs.brand.addEventListener('blur', () => validateText(inputs.brand, 'Brand'));

            inputs.model.addEventListener('input', () => validateText(inputs.model, 'Model'));
            inputs.model.addEventListener('blur', () => validateText(inputs.model, 'Model'));

            inputs.category_id.addEventListener('change', () => validateSelect(inputs.category_id, 'Category'));
            inputs.vendor_id.addEventListener('change', () => validateSelect(inputs.vendor_id, 'Vendor'));
            inputs.status.addEventListener('change', () => validateSelect(inputs.status, 'Status'));

            inputs.cost.addEventListener('input', validateCost);
            inputs.cost.addEventListener('blur', validateCost);

            inputs.purchase_date.addEventListener('change', validateDates);
            inputs.warranty_date.addEventListener('change', validateDates);

            inputs.image.addEventListener('change', validateImage);

            // Submit guard
            form.addEventListener('submit', (e) => {
                const isNameValid = validateText(inputs.name, 'Asset Name');
                const isSerialValid = validateText(inputs.serial_number, 'Serial Number');
                const isBrandValid = validateText(inputs.brand, 'Brand');
                const isModelValid = validateText(inputs.model, 'Model');
                const isCategoryValid = validateSelect(inputs.category_id, 'Category');
                const isVendorValid = validateSelect(inputs.vendor_id, 'Vendor');
                const isStatusValid = validateSelect(inputs.status, 'Status');
                const isCostValid = validateCost();
                const areDatesValid = validateDates();
                const isImageValid = validateImage();

                const isValid = isNameValid && isSerialValid && isBrandValid && isModelValid &&
                    isCategoryValid && isVendorValid && isStatusValid &&
                    isCostValid && areDatesValid && isImageValid;

                if (!isValid) {
                    e.preventDefault();
                    const firstError = form.querySelector('.has-error input, .has-error select');
                    if (firstError) firstError.focus();
                    return;
                }

                submitBtn.disabled = true;
                const btnContent = submitBtn.querySelector('.btn-content');
                if (btnContent) {
                    btnContent.innerHTML = `
                    <svg class="spinner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                    </svg>
                    <span>Saving Changes...</span>
                `;
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