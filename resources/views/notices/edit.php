<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Notice — AssetTracker</title>

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

        textarea {
            width: 100%;
            min-height: 160px;
            resize: vertical;
            padding: 14px 16px;
            border: 1px solid var(--slate-300);
            border-radius: var(--radius-md);
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-delete {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background-color: #ef4444;
            color: #ffffff;
            border: none;
            border-radius: 30px;
            font-family: inherit;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.15s ease;
            text-decoration: none;
        }

        .btn-delete:hover {
            background-color: #dc2626;
            transform: translateY(-1px);
        }

        .btn-delete svg {
            width: 16px;
            height: 16px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
        }
    </style>
</head>

<body>

    <div class="edit-container<?php echo (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN') ? ' admin-edit' : ''; ?>">

        <div class="card">

            <div class="card-header">
                <div class="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </div>
                <h1>Edit Notice</h1>
                <p style="color: var(--slate-400); font-size: 13px; margin-top: 4px;">Update or delete notice information</p>
            </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert-error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>

            <form action="index.php?route=notices/edit&id=<?= (int)$notice['id'] ?>" method="post" id="editNoticeForm" novalidate>

                <?= App\helpers\Csrf::field() ?>
                <div class="form-grid">

                    <!-- Notice Title -->
                    <div class="form-group <?php echo isset($errors['title_id']) ? 'has-error' : ''; ?>">
                        <label for="title_id">
                            Notice Title <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <select name="title_id" id="title_id">
                                <option value="">Select Notice Title</option>
                                <?php $selectedTitle = (int)($old['title_id'] ?? $notice['title_id'] ?? 0); ?>
                                <?php foreach ($noticeTitles ?? [] as $title): ?>
                                    <option value="<?= (int)$title['id'] ?>" <?= $selectedTitle === (int)$title['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($title['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16v16H4z" />
                                <path d="M8 8h8" />
                                <path d="M8 12h8" />
                                <path d="M8 16h5" />
                            </svg>
                        </div>
                        <?php if (isset($errors['title_id'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['title_id']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Message -->
                    <div class="form-group full-width <?php echo isset($errors['message']) ? 'has-error' : ''; ?>">
                        <label for="message">
                            Message <span class="required">*</span>
                        </label>
                        <div class="input-wrapper">
                            <textarea name="message" id="message" placeholder="Enter notice message..."><?= htmlspecialchars($old['message'] ?? $notice['message'] ?? '') ?></textarea>
                        </div>
                        <?php if (isset($errors['message'])): ?>
                            <div class="error-text">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />
                                </svg>
                                <?php echo htmlspecialchars($errors['message']); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Form Actions -->
                    <div class="actions-row" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <div>
                            <button type="button" class="btn-delete" id="deleteBtn" onclick="confirmDelete(<?= (int)$notice['id'] ?>)">
                                <svg viewBox="0 0 24 24">
                                    <polyline points="3 6 5 6 21 6" />
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                                </svg>
                                Delete Notice
                            </button>
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <a href="index.php?route=notices" class="btn-cancel">Cancel</a>
                            <button type="submit" class="btn-submit" id="submitBtn">
                                <span class="btn-content">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                        <polyline points="17 21 17 13 7 13 7 21" />
                                        <polyline points="7 3 7 8 15 8" />
                                    </svg>
                                    <span>Update Notice</span>
                                </span>
                            </button>
                        </div>
                    </div>

                </div>
            </form>

            <form id="deleteForm" action="index.php?route=notices/delete&id=<?= (int)$notice['id'] ?>" method="post" style="display: none;"><?= App\helpers\Csrf::field() ?></form>

        </div>

    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        function confirmDelete(noticeId) {
            if (confirm('Are you sure you want to delete this notice? This action cannot be undone.')) {
                document.getElementById('deleteForm').submit();
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('editNoticeForm');
            if (!form) return;

            const inputs = {
                title_id: document.getElementById('title_id'),
                message: document.getElementById('message')
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

            const validateTitle = () => {
                if (!inputs.title_id.value) {
                    showError(inputs.title_id, 'Please select a notice title.');
                    return false;
                }
                clearError(inputs.title_id);
                return true;
            };

            const validateMessage = () => {
                const value = inputs.message.value.trim();
                if (!value) {
                    showError(inputs.message, 'Message is required.');
                    return false;
                }
                if (value.length < 5) {
                    showError(inputs.message, 'Message must be at least 5 characters.');
                    return false;
                }
                clearError(inputs.message);
                return true;
            };

            inputs.title_id.addEventListener('change', validateTitle);
            inputs.message.addEventListener('input', validateMessage);
            inputs.message.addEventListener('blur', validateMessage);

            form.addEventListener('submit', (e) => {
                const isTitleValid = validateTitle();
                const isMessageValid = validateMessage();

                if (!isTitleValid || !isMessageValid) {
                    e.preventDefault();
                    const firstError = form.querySelector('.has-error input, .has-error select, .has-error textarea');
                    if (firstError) {
                        firstError.focus();
                    }
                    return;
                }

                submitBtn.disabled = true;
                const btnContent = submitBtn.querySelector('.btn-content');
                if (btnContent) {
                    btnContent.innerHTML = `
                        <svg class="spinner-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v4"/>
                            <path d="M12 18v4"/>
                            <path d="M4.93 4.93l2.83 2.83"/>
                            <path d="M16.24 16.24l2.83 2.83"/>
                            <path d="M2 12h4"/>
                            <path d="M18 12h4"/>
                            <path d="M4.93 19.07l2.83-2.83"/>
                            <path d="M16.24 7.76l2.83-2.83"/>
                        </svg>
                        <span>Updating Notice...</span>
                    `;
                }
            });
        });
    </script>

</body>

</html>