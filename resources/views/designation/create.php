<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Designation</title>
</head>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../resources/css/form.css">
<body>
<div class="edit-container<?php echo (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN') ? ' admin-edit' : ''; ?>">
    <div class="card">
        <!-- ========================= -->
        <!-- Card Header -->
        <!-- ========================= -->
        <div class="card-header">
            <div class="icon">
                <svg xmlns="http://www.w3.org/2000/svg"
                     width="24"
                     height="24"
                     viewBox="0 0 24 24"
                     fill="none"
                     stroke="currentColor"
                     stroke-width="2"
                     stroke-linecap="round"
                     stroke-linejoin="round">

                    <!-- ID Card -->
                    <rect x="3" y="5" width="18" height="14" rx="2"/>

                    <!-- Name Line -->
                    <line x1="9" y1="10" x2="17" y2="10"/>
                    <line x1="9" y1="14" x2="15" y2="14"/>

                    <!-- Badge -->
                    <circle cx="6.5" cy="10.5" r="1.3"/>

                    <!-- Rank Star -->
                    <path d="M18 17l.6 1.3 1.4.2-1 1 .2 1.5-1.2-.7-1.2.7.2-1.5-1-1 1.4-.2.6-1.3z"/>
                </svg>
            </div>
            <h1>Add New Designation</h1>
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
        <form action="index.php?route=designations/create" method="post">
            <div class="form-grid">
                <!-- ========================= -->
                <!-- Name Field -->
                <!-- ========================= -->
                <div class="form-group full-width <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                    <label for="name">Designation Name <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="name" id="name" placeholder="ex. Team Lead"
                               value="<?php echo htmlspecialchars($old['name'] ?? $assetData['name'] ?? ''); ?>">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M20 7 12 3 4 7v10l8 4 8-4V7z"/>
                            <path d="M12 21V11"/>
                            <path d="M4 7l8 4 8-4"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['name'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['name']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Form Actions -->
                <!-- Cancel & Save Buttons -->
                <!-- ========================= -->
                <div class="actions-row">
                    <a href="index.php?route=designations" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">
                        <span class="btn-content">
                            <svg viewBox="0 0 24 24"><path
                                        d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline
                                        points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Add Department
                        </span>
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
</body>
</html>