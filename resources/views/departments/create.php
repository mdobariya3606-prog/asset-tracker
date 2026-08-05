<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create user</title>
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

                    <!-- Main Department -->
                    <rect x="9" y="3" width="6" height="4" rx="1"/>

                    <!-- Connection -->
                    <path d="M12 7v3"/>
                    <path d="M6 10h12"/>

                    <!-- Sub Departments -->
                    <rect x="3" y="10" width="6" height="4" rx="1"/>
                    <rect x="15" y="10" width="6" height="4" rx="1"/>

                    <!-- Bottom Connections -->
                    <path d="M6 14v3"/>
                    <path d="M18 14v3"/>
                    <path d="M6 17h12"/>

                    <!-- Teams -->
                    <rect x="3" y="17" width="6" height="4" rx="1"/>
                    <rect x="15" y="17" width="6" height="4" rx="1"/>

                </svg>
            </div>
            <h1>Add New Department</h1>
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
        <form action="index.php?route=departments/create" method="post">
            <div class="form-grid">
                <!-- ========================= -->
                <!-- Name Field -->
                <!-- ========================= -->
                <div class="form-group full-width <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                    <label for="name">Department Name <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="name" id="name" placeholder="ex. Finance"
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
                    <a href="index.php?route=departments" class="btn-cancel">Cancel</a>
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