<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Send Personal Notice — AssetTracker</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="resources/css/form.css">

    <style>
        textarea {
            width: 100%;
            min-height: 150px;
            resize: vertical;
            padding: 14px;
            box-sizing: border-box;
        }

        .error-text {
            color: #dc2626;
            font-size: 12px;
            margin-top: 6px;
        }

        .form-group select,
        .form-group textarea {
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="edit-container">
        <div class="card">

            <div class="card-header">
                <div class="icon">
                    <svg
                        width="28"
                        height="28"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true">
                        <path
                            d="M4 4H20C21.1 4 22 4.9 22 6V18C22 19.1 21.1 20 20 20H4C2.9 20 2 19.1 2 18V6C2 4.9 2.9 4 4 4Z"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path
                            d="M22 6L12 13L2 6"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </div>

                <h1>Send Personal Notice</h1>
            </div>

            <?php

            use App\helpers\Csrf;

            if (!empty($errors['general'])):
            ?>
                <div class="alert-error">
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form
                action="index.php?route=notices/create-custom"
                method="post"
                novalidate>
                <?= Csrf::field() ?>

                <div class="form-grid">

                    <!-- Employee -->
                    <div class="form-group <?= isset($errors['employee_id']) ? 'has-error' : '' ?>">
                        <label for="employee_id">
                            Employee
                            <span class="required">*</span>
                        </label>

                        <select name="employee_id" id="employee_id" required>
                            <option value="">Select employee</option>

                            <?php foreach ($employees ?? [] as $employee): ?>
                                <option
                                    value="<?= (int) $employee['id'] ?>"
                                    <?= (int) ($old['employee_id'] ?? 0) === (int) $employee['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($employee['name']) ?>
                                    —
                                    <?= htmlspecialchars($employee['email']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <?php if (isset($errors['employee_id'])): ?>
                            <div class="error-text">
                                <?= htmlspecialchars($errors['employee_id']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Related Asset -->
                    <div class="form-group <?= isset($errors['asset_id']) ? 'has-error' : '' ?>">
                        <label for="asset_id">
                            Related Asset
                            <span style="font-weight: normal">(optional)</span>
                        </label>

                        <select name="asset_id" id="asset_id">
                            <option value="">No asset</option>

                            <?php foreach ($assets ?? [] as $asset): ?>
                                <option
                                    value="<?= (int) $asset['id'] ?>"
                                    <?= (int) ($old['asset_id'] ?? 0) === (int) $asset['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($asset['name']) ?>

                                    <?php if (!empty($asset['serial_number'])): ?>
                                        —
                                        <?= htmlspecialchars($asset['serial_number']) ?>
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <?php if (isset($errors['asset_id'])): ?>
                            <div class="error-text">
                                <?= htmlspecialchars($errors['asset_id']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Notice Title -->
                    <div class="form-group <?= isset($errors['title_id']) ? 'has-error' : '' ?>">
                        <label for="title_id">
                            Notice Title
                            <span class="required">*</span>
                        </label>

                        <select name="title_id" id="title_id" required>
                            <option value="">Select notice title</option>

                            <?php foreach ($noticeTitles ?? [] as $title): ?>
                                <option
                                    value="<?= (int) $title['id'] ?>"
                                    <?= (int) ($old['title_id'] ?? 0) === (int) $title['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($title['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <?php if (isset($errors['title_id'])): ?>
                            <div class="error-text">
                                <?= htmlspecialchars($errors['title_id']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Message -->
                    <div class="form-group full-width <?= isset($errors['message']) ? 'has-error' : '' ?>">
                        <label for="message">
                            Message
                            <span class="required">*</span>
                        </label>

                        <textarea
                            name="message"
                            id="message"
                            required><?= htmlspecialchars($old['message'] ?? '') ?></textarea>

                        <?php if (isset($errors['message'])): ?>
                            <div class="error-text">
                                <?= htmlspecialchars($errors['message']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Actions -->
                    <div class="actions-row">
                        <a
                            href="index.php?route=notices"
                            class="btn-cancel">
                            Cancel
                        </a>

                        <button
                            type="submit"
                            class="btn-submit">
                            Send Notice
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>
</body>

</html>