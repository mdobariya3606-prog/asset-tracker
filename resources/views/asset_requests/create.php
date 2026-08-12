<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ========================= -->
    <!-- Page Metadata & Assets -->
    <!-- ========================= -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Asset — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/form.css">
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

        .textarea-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 6px;
            font-size: 0.8125rem;
            color: #64748b;
        }
    </style>
</head>

<body>
    <!-- ========================= -->
    <!-- Request Asset Page Container -->
    <!-- ========================= -->
    <div class="edit-container">
        <div class="card">

            <!-- ========================= -->
            <!-- Card Header -->
            <!-- ========================= -->
            <div class="card-header">
                <div class="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1" />
                        <path d="M9 12h6" />
                        <path d="M9 16h6" />
                    </svg>
                </div>
                <h1>Request <?= htmlspecialchars($asset['name'] ?? 'Asset') ?></h1>
                <p>Explain the business need and reason for requesting this asset.</p>
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
            <!-- Request Asset Form -->
            <!-- ========================= -->
            <form action="index.php?route=assets/request&id=<?= (int)($_GET['id'] ?? 0); ?>" method="post"
                id="requestAssetForm" novalidate>
                <div class="form-grid">

                    <!-- ========================= -->
                    <!-- Reason Field -->
                    <!-- ========================= -->
                    <div class="form-group full-width <?php echo isset($errors['reason']) ? 'has-error' : ''; ?>">
                        <label for="reason">Reason for Request <span class="required">*</span></label>
                        <div class="input-wrapper">
                            <textarea name="reason" id="reason" rows="4"
                                placeholder="ex. Existing asset has reached end of life and requires replacement."
                                autofocus><?php echo htmlspecialchars($old['reason'] ?? ''); ?></textarea>
                        </div>

                        <div class="textarea-meta">
                            <span id="reasonHint">Minimum 10 characters required.</span>
                            <span id="charCount">0 characters</span>
                        </div>

                        <?php if (isset($errors['reason'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['reason']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- ========================= -->
                    <!-- Form Actions -->
                    <!-- Cancel & Submit Buttons -->
                    <!-- ========================= -->
                    <div class="actions-row">
                        <a href="index.php?route=assets/show&id=<?= (int)($_GET['id'] ?? 0); ?>"
                            class="btn-cancel">Cancel</a>
                        <button type="submit" class="btn-submit" id="submitBtn">
                            <span class="btn-content">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="22" y1="2" x2="11" y2="13" />
                                    <polygon points="22 2 15 22 11 13 2 9 22 2" />
                                </svg>
                                <span>Make Request</span>
                            </span>
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('requestAssetForm');
            const reasonInput = document.getElementById('reason');
            const charCountEl = document.getElementById('charCount');
            const submitBtn = document.getElementById('submitBtn');

            if (!form || !reasonInput) return;

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

            const showError = (message) => {
                const group = reasonInput.closest('.form-group');
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

            const clearError = () => {
                const group = reasonInput.closest('.form-group');
                if (!group) return;

                group.classList.remove('has-error');
                const errorEl = group.querySelector('.error-text');
                if (errorEl) {
                    errorEl.remove();
                }
            };

            const updateCharCount = () => {
                const len = reasonInput.value.length;
                if (charCountEl) {
                    charCountEl.textContent = `${len} character${len === 1 ? '' : 's'}`;
                }
            };

            const validateReason = () => {
                const val = reasonInput.value.trim();
                if (!val) {
                    showError('Please provide a reason for requesting this asset.');
                    return false;
                }
                if (val.length < 10) {
                    showError('Reason must be at least 10 characters long.');
                    return false;
                }
                clearError();
                return true;
            };

            // Real-time counter and error handling
            reasonInput.addEventListener('input', () => {
                updateCharCount();
                if (reasonInput.value.trim().length >= 10) {
                    clearError();
                }
            });

            reasonInput.addEventListener('blur', validateReason);

            // Initialize count on page load
            updateCharCount();

            // Form Submission
            form.addEventListener('submit', (e) => {
                if (!validateReason()) {
                    e.preventDefault();
                    reasonInput.focus();
                    return;
                }

                submitBtn.disabled = true;
                const btnContent = submitBtn.querySelector('.btn-content');
                if (btnContent) {
                    btnContent.innerHTML = `
                    <svg class="spinner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                    </svg>
                    <span>Submitting Request...</span>
                `;
                }
            });
        });
    </script>
</body>

</html>