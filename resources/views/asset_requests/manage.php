<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ========================= -->
    <!-- Page Metadata & Assets -->
    <!-- ========================= -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Request — AssetTracker</title>
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

        .is-hidden {
            display: none !important;
        }
    </style>
</head>

<body>
    <!-- ========================= -->
    <!-- Manage Request Page Container -->
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
                <h1>Manage Request</h1>
                <p>Update request details
                    for <?php echo htmlspecialchars($old['name'] ?? $assetRequest['asset_name'] ?? 'Asset'); ?></p>
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
            <!-- Manage Request Form -->
            <!-- ========================= -->
            <form action="index.php?route=assets/requests/manage&id=<?= (int)($_GET['id'] ?? 0) ?>" method="post"
                id="manageRequestForm" novalidate>
                <div class="form-grid">

                    <!-- ========================= -->
                    <!-- Status Selection -->
                    <!-- ========================= -->
                    <div class="form-group full-width <?php echo isset($errors['status']) ? 'has-error' : ''; ?>">
                        <label for="status">Status <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <select id="status" name="status" autofocus>
                                <option value="">Select Status</option>
                                <?php $selectedStatus = strtoupper($old['status'] ?? $assetRequest['status'] ?? 'PENDING'); ?>
                                <?php foreach ($statusEnum ?? ['PENDING', 'APPROVED', 'REJECTED'] as $status): ?>
                                    <option value="<?= strtoupper($status) ?>" <?= ($selectedStatus === strtoupper($status)) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(ucfirst(strtolower($status))) ?>
                                    </option>
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
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['status']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Remark Field -->
                    <!-- ========================= -->
                    <div class="form-group full-width <?php echo isset($errors['remark']) ? 'has-error' : ''; ?>">
                        <label for="remark">Remark (optional)</label>
                        <div class="input-wrapper">
                            <input type="text" name="remark" id="remark" placeholder="ex. Approved by department manager."
                                value="<?php echo htmlspecialchars($old['remark'] ?? $assetRequest['remark'] ?? ''); ?>">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                            </svg>
                        </div>
                        <?php if (isset($errors['remark'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['remark']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Rejection Reason Field (Hidden by default, optional when visible) -->
                    <!-- ========================= -->
                    <div class="form-group full-width <?php echo isset($errors['rejection_reason']) ? 'has-error' : ''; ?>"
                        id="rejectionGroup">
                        <label for="rejection_reason">Rejection Reason (optional)</label>
                        <div class="input-wrapper">
                            <input type="text" name="rejection_reason" id="rejection_reason"
                                placeholder="ex. Request does not meet business requirements."
                                value="<?php echo htmlspecialchars($old['rejection_reason'] ?? $assetRequest['rejection_reason'] ?? ''); ?>">
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.59 13.41L11 3H4v7l9.59 9.59a2 2 0 0 0 2.82 0l4.18-4.18a2 2 0 0 0 0-2.82z" />
                                <circle cx="7.5" cy="7.5" r="1.5" />
                            </svg>
                        </div>
                        <?php if (isset($errors['rejection_reason'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['rejection_reason']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Form Actions -->
                    <!-- ========================= -->
                    <div class="actions-row">
                        <a href="index.php?route=assets/requests/show&id=<?= (int)($_GET['id'] ?? 0); ?>"
                            class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span class="btn-content">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                    <polyline points="17 21 17 13 7 13 7 21" />
                                    <polyline points="7 3 7 8 15 8" />
                                </svg>
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
            const form = document.getElementById('manageRequestForm');
            const statusSelect = document.getElementById('status');
            const rejectionGroup = document.getElementById('rejectionGroup');
            const submitBtn = document.getElementById('submitBtn');

            if (!form) return;

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

            // Show rejection field ONLY when 'REJECTED' is selected
            const toggleRejectionField = () => {
                if (statusSelect.value === 'REJECTED') {
                    rejectionGroup.classList.remove('is-hidden');
                } else {
                    rejectionGroup.classList.add('is-hidden');
                }
            };

            const validateStatus = () => {
                if (!statusSelect.value) {
                    showError(statusSelect, 'Please select a status.');
                    return false;
                }
                clearError(statusSelect);
                return true;
            };

            // Event listener for status changes
            statusSelect.addEventListener('change', () => {
                toggleRejectionField();
                validateStatus();
            });

            // Run initially on page load
            toggleRejectionField();

            // Submit guard
            form.addEventListener('submit', (e) => {
                if (!validateStatus()) {
                    e.preventDefault();
                    statusSelect.focus();
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
</body>

</html>