<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Notice — AssetTracker</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">

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
                    <svg viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">

                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" />
                        <path d="M13.73 21a2 2 0 0 1-3.46 0" />

                    </svg>
                </div>

                <h1>Create General Notice</h1>

            </div>


            <!-- ========================= -->
            <!-- General Validation Error -->
            <!-- ========================= -->

            <?php if (!empty($errors['general'])): ?>

                <div class="alert-error">

                    <svg viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round">

                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />

                    </svg>

                    <?php echo htmlspecialchars($errors['general']); ?>

                </div>

            <?php endif; ?>


            <!-- ========================= -->
            <!-- Create Notice Form -->
            <!-- ========================= -->

            <form action="index.php?route=notices/create"
                method="post"
                id="createNoticeForm"
                novalidate>

                <div class="form-grid">


                    <!-- ========================= -->
                    <!-- Notice Title -->
                    <!-- ========================= -->

                    <div class="form-group <?php echo isset($errors['title_id']) ? 'has-error' : ''; ?>">

                        <label for="title_id">
                            Notice Title <span class="required">*</span>
                        </label>

                        <div class="input-wrapper">

                            <select name="title_id" id="title_id">

                                <option value="">Select Notice Title</option>

                                <?php $selectedTitle = (int)($old['title_id'] ?? 0);
                                ?>

                                <?php foreach ($noticeTitles ?? [] as $title): ?>

                                    <option
                                        value="<?= (int)$title['id'] ?>"
                                        <?= $selectedTitle === (int)$title['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($title['title']) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <svg class="input-icon"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round">

                                <path d="M4 4h16v16H4z" />
                                <path d="M8 8h8" />
                                <path d="M8 12h8" />
                                <path d="M8 16h5" />

                            </svg>

                        </div>

                        <?php if (isset($errors['title_id'])): ?>

                            <div class="error-text">

                                <svg viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round">

                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />

                                </svg>

                                <?php echo htmlspecialchars($errors['title_id']); ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- ========================= -->
                    <!-- Message -->
                    <!-- ========================= -->

                    <div class="form-group full-width <?php echo isset($errors['message']) ? 'has-error' : ''; ?>">

                        <label for="message">
                            Message <span class="required">*</span>
                        </label>

                        <div class="input-wrapper">

                            <textarea
                                name="message"
                                id="message"
                                placeholder="Enter notice message..."><?= htmlspecialchars($old['message'] ?? '') ?></textarea>

                        </div>

                        <?php if (isset($errors['message'])): ?>

                            <div class="error-text">

                                <svg viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round">

                                    <circle cx="12" cy="12" r="10" />
                                    <line x1="12" y1="8" x2="12" y2="12" />
                                    <line x1="12" y1="16" x2="12.01" y2="16" />

                                </svg>

                                <?php echo htmlspecialchars($errors['message']); ?>

                            </div>

                        <?php endif; ?>

                    </div>


                    <!-- ========================= -->
                    <!-- Form Actions -->
                    <!-- ========================= -->

                    <div class="actions-row">

                        <a href="index.php?route=notices"
                            class="btn-cancel">
                            Cancel
                        </a>

                        <button type="submit"
                            class="btn-submit"
                            id="submitBtn">

                            <span class="btn-content">

                                <svg viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round">

                                    <path d="M22 2 11 13" />
                                    <path d="m22 2-7 20-4-9-9-4Z" />

                                </svg>

                                <span>Send Notice</span>

                            </span>

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const form = document.getElementById('createNoticeForm');

            if (!form) return;


            const inputs = {
                title_id: document.getElementById('title_id'),
                message: document.getElementById('message')
            };


            const submitBtn = document.getElementById('submitBtn');


            // =========================
            // Create Error SVG
            // =========================

            const createErrorSvg = () => {

                const svg = document.createElementNS(
                    'http://www.w3.org/2000/svg',
                    'svg'
                );

                svg.setAttribute('viewBox', '0 0 24 24');
                svg.setAttribute('fill', 'none');
                svg.setAttribute('stroke', 'currentColor');
                svg.setAttribute('stroke-width', '2');
                svg.setAttribute('stroke-linecap', 'round');
                svg.setAttribute('stroke-linejoin', 'round');


                const circle = document.createElementNS(
                    'http://www.w3.org/2000/svg',
                    'circle'
                );

                circle.setAttribute('cx', '12');
                circle.setAttribute('cy', '12');
                circle.setAttribute('r', '10');


                const line1 = document.createElementNS(
                    'http://www.w3.org/2000/svg',
                    'line'
                );

                line1.setAttribute('x1', '12');
                line1.setAttribute('y1', '8');
                line1.setAttribute('x2', '12');
                line1.setAttribute('y2', '12');


                const line2 = document.createElementNS(
                    'http://www.w3.org/2000/svg',
                    'line'
                );

                line2.setAttribute('x1', '12');
                line2.setAttribute('y1', '16');
                line2.setAttribute('x2', '12.01');
                line2.setAttribute('y2', '16');


                svg.appendChild(circle);
                svg.appendChild(line1);
                svg.appendChild(line2);

                return svg;
            };


            // =========================
            // Show Error
            // =========================

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

                errorEl.appendChild(
                    document.createTextNode(' ' + message)
                );
            };


            // =========================
            // Clear Error
            // =========================

            const clearError = (input) => {

                const group = input.closest('.form-group');

                if (!group) return;

                group.classList.remove('has-error');


                const errorEl = group.querySelector('.error-text');

                if (errorEl) {
                    errorEl.remove();
                }
            };


            // =========================
            // Validate Title
            // =========================

            const validateTitle = () => {

                if (!inputs.title_id.value) {

                    showError(
                        inputs.title_id,
                        'Please select a notice title.'
                    );

                    return false;
                }

                clearError(inputs.title_id);

                return true;
            };


            // =========================
            // Validate Message
            // =========================

            const validateMessage = () => {

                const value = inputs.message.value.trim();


                if (!value) {

                    showError(
                        inputs.message,
                        'Message is required.'
                    );

                    return false;
                }


                if (value.length < 5) {

                    showError(
                        inputs.message,
                        'Message must be at least 5 characters.'
                    );

                    return false;
                }


                clearError(inputs.message);

                return true;
            };


            // =========================
            // Event Listeners
            // =========================

            inputs.title_id.addEventListener(
                'change',
                validateTitle
            );


            inputs.message.addEventListener(
                'input',
                validateMessage
            );


            inputs.message.addEventListener(
                'blur',
                validateMessage
            );


            // =========================
            // Submit Validation
            // =========================

            form.addEventListener('submit', (e) => {

                const isTitleValid = validateTitle();

                const isMessageValid = validateMessage();


                const isValid =
                    isTitleValid &&
                    isMessageValid;


                if (!isValid) {

                    e.preventDefault();

                    const firstError =
                        form.querySelector(
                            '.has-error input, .has-error select, .has-error textarea'
                        );


                    if (firstError) {
                        firstError.focus();
                    }

                    return;
                }


                // =========================
                // Submit Spinner
                // =========================

                submitBtn.disabled = true;

                const btnContent =
                    submitBtn.querySelector('.btn-content');


                if (btnContent) {

                    btnContent.innerHTML = `
                <svg class="spinner-icon"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">

                    <path d="M12 2v4"/>
                    <path d="M12 18v4"/>
                    <path d="M4.93 4.93l2.83 2.83"/>
                    <path d="M16.24 16.24l2.83 2.83"/>
                    <path d="M2 12h4"/>
                    <path d="M18 12h4"/>
                    <path d="M4.93 19.07l2.83-2.83"/>
                    <path d="M16.24 7.76l2.83-2.83"/>

                </svg>

                <span>Sending Notice...</span>
            `;
                }

            });

        });
    </script>

</body>

</html>