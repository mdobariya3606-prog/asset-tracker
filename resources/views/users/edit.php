<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Subtle decorative blobs */
        body::before, body::after {
            content: '';
            position: fixed;
            border-radius: 50%;
            filter: blur(80px);
            opacity: .35;
            z-index: 0;
            pointer-events: none;
        }
        body::before {
            width: 420px; height: 420px;
            background: radial-gradient(circle, #3b82f6 0%, transparent 70%);
            top: -120px; right: -100px;
        }
        body::after {
            width: 350px; height: 350px;
            background: radial-gradient(circle, #06b6d4 0%, transparent 70%);
            bottom: -80px; left: -60px;
        }

        .edit-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 580px;
        }

        /* Admins edit additional role and organisation fields, so give the
           two-column form enough room to keep labels and controls aligned. */
        .edit-container.admin-edit {
            max-width: 760px;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            padding: 44px 40px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        }

        .card-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .card-header .icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #3b82f6, #06b6d4);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
        }

        .card-header .icon svg {
            width: 28px; height: 28px;
            fill: none;
            stroke: #fff;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .card-header h1 {
            font-size: 26px;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: -0.5px;
        }

        .card-header p {
            color: #94a3b8;
            font-size: 14px;
            margin-top: 6px;
        }

        /* Error alert */
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #7f1d1d;
            border-radius: 12px;
            padding: 14px 18px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.4s ease;
        }

        .alert-error svg {
            width: 20px; height: 20px; flex-shrink: 0;
            stroke: #f87171; fill: none; stroke-width: 2;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Form grid */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-grid .full-width {
            grid-column: 1 / -1;
        }

        .form-group {
            position: relative;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #475569;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .form-group label .required {
            color: #f87171;
            margin-left: 2px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px; height: 18px;
            stroke: #94a3b8;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: stroke 0.3s;
            pointer-events: none;
            z-index: 10;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 14px 12px 44px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            color: #1e293b;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .form-group select {
            padding-right: 40px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 6l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            cursor: pointer;
        }

        .form-group select option {
            background: #fff;
            color: #1e293b;
        }

        .form-group input::placeholder {
            color: #94a3b8;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .form-group input:focus ~ .input-icon,
        .form-group select:focus ~ .input-icon {
            stroke: #3b82f6;
        }

        /* Error state */
        .form-group.has-error input,
        .form-group.has-error select {
            border-color: #f87171;
            background: rgba(248, 113, 113, 0.06);
        }

        .form-group.has-error input:focus,
        .form-group.has-error select:focus {
            box-shadow: 0 0 0 4px rgba(248, 113, 113, 0.15);
        }

        .form-group.has-error .input-icon {
            stroke: #f87171;
        }

        .error-text {
            color: #ef4444;
            font-size: 12px;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
            animation: slideDown 0.3s ease;
        }

        .error-text svg {
            width: 14px; height: 14px; flex-shrink: 0;
            stroke: #f87171; fill: none; stroke-width: 2;
        }

        /* Actions row */
        .actions-row {
            grid-column: 1 / -1;
            display: flex;
            gap: 12px;
            margin-top: 12px;
        }

        .btn-submit {
            flex: 1;
            padding: 14px;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: 0.3px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent, rgba(255,255,255,0.15), transparent);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(124, 58, 237, 0.35);
        }

        .btn-submit:hover::before {
            transform: translateX(100%);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit .btn-content {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit svg {
            width: 18px; height: 18px;
            stroke: currentColor; fill: none;
            stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
        }

        .btn-cancel {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 14px 24px;
            background: #fff;
            color: #475569;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #1e293b;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .card { padding: 32px 24px; }
            .form-grid { grid-template-columns: 1fr; }
            .form-grid .full-width { grid-column: auto; }
            .card-header h1 { font-size: 22px; }
            .actions-row { flex-direction: column-reverse; }
        }
    </style>
</head>
<body>
<div class="edit-container<?= !empty($isAdmin) ? ' admin-edit' : '' ?>">
    <div class="card">

        <div class="card-header">
            <div class="icon">
                <svg viewBox="0 0 24 24">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/>
                </svg>
            </div>
            <h1>Edit User Profile</h1>
            <p>Modify details for <?php echo htmlspecialchars($old['name'] ?? $user['name'] ?? 'User'); ?></p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert-error">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?php echo htmlspecialchars($errors['general']); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?route=users/edit&id=<?php echo $user['id']; ?>" method="post" novalidate id="editForm">
            <div class="form-grid">

                <!-- Name -->
                <div class="form-group full-width <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="name" id="name"
                               placeholder="Enter full name"
                               value="<?php echo htmlspecialchars($old['name'] ?? $user['name'] ?? ''); ?>">
                        <svg class="input-icon" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <?php if (isset($errors['name'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <?php echo htmlspecialchars($errors['name']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Email -->
                <div class="form-group full-width <?php echo isset($errors['email']) ? 'has-error' : ''; ?>">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="email" name="email" id="email"
                               placeholder="name@company.com"
                               value="<?php echo htmlspecialchars($old['email'] ?? $user['email'] ?? ''); ?>">
                        <svg class="input-icon" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </div>
                    <?php if (isset($errors['email'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <?php echo htmlspecialchars($errors['email']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Mobile -->
                <div class="form-group <?php echo isset($errors['mobile']) ? 'has-error' : ''; ?>">
                    <label for="mobile">Mobile <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="mobile" id="mobile"
                               placeholder="10-digit number"
                               value="<?php echo htmlspecialchars($old['mobile'] ?? $user['mobile'] ?? ''); ?>"
                               maxlength="10">
                        <svg class="input-icon" viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                    </div>
                    <?php if (isset($errors['mobile'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <?php echo htmlspecialchars($errors['mobile']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (empty($isOwnProfile) || ($_SESSION['user_role'] ?? '') === 'ADMIN'): ?>
                <!-- Role -->
                <div class="form-group <?php echo isset($errors['role']) ? 'has-error' : ''; ?>">
                    <label for="role">Role <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <?php 
                            $currentRole = strtoupper($old['role'] ?? $user['role'] ?? 'EMPLOYEE');
                        ?>
                        <select name="role" id="role">
                            <option value="EMPLOYEE" <?php echo ($currentRole == 'EMPLOYEE') ? 'selected' : ''; ?>>Employee</option>
                            <option value="MANAGER" <?php echo ($currentRole == 'MANAGER') ? 'selected' : ''; ?>>Manager</option>
                            <option value="HR" <?php echo ($currentRole == 'HR') ? 'selected' : ''; ?>>HR</option>
                            <option value="ADMIN" <?php echo ($currentRole == 'ADMIN') ? 'selected' : ''; ?>>Admin</option>
                        </select>
                        <svg class="input-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <?php if (isset($errors['role'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <?php echo htmlspecialchars($errors['role']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Department -->
                <div class="form-group <?php echo isset($errors['department_id']) ? 'has-error' : ''; ?>">
                    <label for="department_id">Department <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select name="department_id" id="department_id">
                            <option value="">Select department</option>
                            <?php 
                                $currentDept = $old['department_id'] ?? $user['department_id'] ?? '';
                                foreach ($departments ?? [] as $department): 
                            ?>
                                <option value="<?php echo $department['id']; ?>"
                                    <?php echo ($currentDept == $department['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($department['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="input-icon" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <?php if (isset($errors['department_id'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <?php echo htmlspecialchars($errors['department_id']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Designation -->
                <div class="form-group <?php echo isset($errors['designation_id']) ? 'has-error' : ''; ?>">
                    <label for="designation_id">Designation <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select name="designation_id" id="designation_id">
                            <option value="">Select designation</option>
                            <?php 
                                $currentDesig = $old['designation_id'] ?? $user['designation_id'] ?? '';
                                foreach ($designations ?? [] as $designation): 
                            ?>
                                <option value="<?php echo $designation['id']; ?>"
                                    <?php echo ($currentDesig == $designation['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($designation['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="input-icon" viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    </div>
                    <?php if (isset($errors['designation_id'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <?php echo htmlspecialchars($errors['designation_id']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Submit & Cancel -->
                <div class="actions-row">
                    <a href="index.php?route=users" class="btn-cancel">
                        Cancel
                    </a>
                    <button type="submit" class="btn-submit">
                        <span class="btn-content">
                            <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                            Save Changes
                        </span>
                    </button>
                </div>

            </div>
        </form>

    </div>
</div>

<script>
    // Prevent double-submit
    document.getElementById('editForm')?.addEventListener('submit', function() {
        const btn = this.querySelector('.btn-submit');
        if (btn) {
            btn.disabled = true;
            btn.style.opacity = '0.7';
            btn.querySelector('.btn-content').innerHTML = `
                <svg viewBox="0 0 24 24" style="animation:spin .8s linear infinite;width:18px;height:18px;stroke:currentColor;fill:none;stroke-width:2">
                    <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                </svg>
                Saving...
            `;
        }
    });
</script>
<style>@keyframes spin { to { transform: rotate(360deg); } }</style>
</body>
</html>
