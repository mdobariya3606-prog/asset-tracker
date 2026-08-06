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
    <link rel="stylesheet" href="../resources/css/form.css">
</head>
<body>
<!-- ========================= -->
<!-- Manage Request Page -->
<!-- ========================= -->
<div class="edit-container<?php echo (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN') ? ' admin-edit' : ''; ?>">
    <div class="card">
        <!-- ========================= -->
        <!-- Card Header -->
        <!-- ========================= -->
        <div class="card-header">
            <div class="icon">
                <?php if (!empty($assetData['profile_image'])): ?>
                    <img src="../storage/profile_images/<?= htmlspecialchars($assetData['profile_image']) ?>"
                         alt="Profile image">
                <?php else: ?>
                    <svg viewBox="0 0 24 24">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/>
                    </svg>
                <?php endif; ?>
            </div>
            <h1>Manage Request</h1>
            <p>Manage Request
                for <?php echo htmlspecialchars($old['name'] ?? $assetData['asset_name'] ?? 'Asset'); ?></p>
        </div>
        <!-- ========================= -->
        <!-- General Validation Error -->
        <!-- ========================= -->
        <?php if (!empty($errors['general'])): ?>
            <div class="alert-error">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                <?php echo htmlspecialchars($errors['general']); ?>
            </div>
        <?php endif; ?>

        <!-- ========================= -->
        <!-- Edit User Form -->
        <!-- ========================= -->
        <form action="index.php?route=assets/requests/manage&id=<?= (int)($_GET['id'] ?? 0) ?>" method="post">
            <div class="form-grid">

                <!-- ========================= -->
                <!-- Status Selection -->
                <!-- ========================= -->
                <div class="form-group <?php echo isset($errors['status']) ? 'has-error' : ''; ?>">
                    <label for="status">Status <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select id="status" name="status">
                            <?php $selectedStatus = ($assetData['status'] ?? 'PENDING'); ?>
                            <?php foreach ($statusEnum ?? [] as $status): ?>
                                <option value="<?= strtoupper($status) ?>" <?= $selectedStatus === $status ? 'selected' : '' ?>><?= htmlspecialchars($status) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <line x1="4" y1="21" x2="4" y2="14"/>
                            <line x1="4" y1="10" x2="4" y2="3"/>
                            <line x1="12" y1="21" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12" y2="3"/>
                            <line x1="20" y1="21" x2="20" y2="16"/>
                            <line x1="20" y1="12" x2="20" y2="3"/>
                            <circle cx="4" cy="12" r="2"/>
                            <circle cx="12" cy="10" r="2"/>
                            <circle cx="20" cy="14" r="2"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['status'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg><?php echo htmlspecialchars($errors['status']); ?></div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Remark -->
                <!-- ========================= -->
                <div class="form-group full-width <?php echo isset($errors['remark']) ? 'has-error' : ''; ?>">
                    <label for="remark">Remark (optional)</label>
                    <div class="input-wrapper">
                        <input type="text" name="remark" id="remark" placeholder="ex. Approved by department manager."
                               value="<?php echo htmlspecialchars($old['remark'] ?? $assetData['remark'] ?? ''); ?>">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M20.59 13.41L11 3H4v7l9.59 9.59a2 2 0 0 0 2.82 0l4.18-4.18a2 2 0 0 0 0-2.82z"/>
                            <circle cx="7.5" cy="7.5" r="1.5"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['remark'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['remark']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Rejection reason -->
                <!-- ========================= -->
                <div class="form-group full-width <?php echo isset($errors['remark']) ? 'has-error' : ''; ?>">
                    <label for="rejection_reason">Rejection reason (optional)</label>
                    <div class="input-wrapper">
                        <input type="text" name="rejection_reason" id="rejection_reason"
                               placeholder="ex. Approved by department manager."
                               value="<?php echo htmlspecialchars($old['rejection_reason'] ?? $assetData['rejection_reason'] ?? ''); ?>">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M20.59 13.41L11 3H4v7l9.59 9.59a2 2 0 0 0 2.82 0l4.18-4.18a2 2 0 0 0 0-2.82z"/>
                            <circle cx="7.5" cy="7.5" r="1.5"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['remark'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['remark']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Form Actions -->
                <!-- Cancel & Save Buttons -->
                <!-- ========================= -->
                <div class="actions-row">
                    <a href="index.php?route=assets/requests/show&id=<?= $_GET['id']; ?>"
                       class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">
                        <span class="btn-content">
                            <svg viewBox="0 0 24 24"><path
                                        d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline
                                        points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Save Changes
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</body>
</html>