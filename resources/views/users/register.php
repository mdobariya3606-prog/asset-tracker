<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register User — AssetTracker</title>
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



        .register-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 580px;
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

        /* Success alert */
        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
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

        .alert-success svg {
            width: 20px; height: 20px; flex-shrink: 0;
            stroke: #34d399; fill: none; stroke-width: 2;
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
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='rgba(255,255,255,0.4)' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 6l4 4 4-4'/%3E%3C/svg%3E");
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

        /* Password toggle */
        .pass-toggle {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            display: flex;
        }

        .pass-toggle svg {
            width: 18px; height: 18px;
            stroke: #94a3b8;
            fill: none; stroke-width: 2;
            transition: stroke 0.2s;
        }

        .pass-toggle:hover svg {
            stroke: #475569;
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 14px;
            margin-top: 8px;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
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
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.35);
        }

        .btn-submit:hover::before {
            transform: translateX(100%);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Footer link */
        .form-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .form-footer span {
            color: #94a3b8;
            font-size: 13px;
        }

        .form-footer a {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .form-footer a:hover {
            color: #2563eb;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .card { padding: 32px 24px; }
            .form-grid { grid-template-columns: 1fr; }
            .card-header h1 { font-size: 22px; }
        }
    </style>
</head>
<body>
<div class="register-container">
    <div class="card">

        <div class="card-header">
            <div class="icon">
                <svg viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <line x1="19" y1="8" x2="19" y2="14"/>
                    <line x1="22" y1="11" x2="16" y2="11"/>
                </svg>
            </div>
            <h1>Create Account</h1>
            <p>Fill in the details to register a new user</p>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert-success">
                <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form action="index.php?route=users/create" method="post" novalidate>
            <div class="form-grid">

                <!-- Name -->
                <div class="form-group full-width <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                    <label for="name">Full Name <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="name" id="name"
                               placeholder="Enter full name"
                               value="<?php echo htmlspecialchars($old['name'] ?? ''); ?>">
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
                               value="<?php echo htmlspecialchars($old['email'] ?? ''); ?>">
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
                               value="<?php echo htmlspecialchars($old['mobile'] ?? ''); ?>"
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

                <!-- Department -->
                <div class="form-group <?php echo isset($errors['department_id']) ? 'has-error' : ''; ?>">
                    <label for="department_id">Department <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select name="department_id" id="department_id">
                            <option value="">Select department</option>
                            <?php foreach ($departments ?? [] as $department): ?>
                                <option value="<?php echo $department['id']; ?>"
                                    <?php echo (isset($old['department_id']) && $old['department_id'] == $department['id']) ? 'selected' : ''; ?>>
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
                            <?php foreach ($designations ?? [] as $designation): ?>
                                <option value="<?php echo $designation['id']; ?>"
                                    <?php echo (isset($old['designation_id']) && $old['designation_id'] == $designation['id']) ? 'selected' : ''; ?>>
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

                <!-- Role -->
                <div class="form-group <?php echo isset($errors['role']) ? 'has-error' : ''; ?>">
                    <label for="role">Role <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select name="role" id="role">
                            <option value="EMPLOYEE" <?php echo (isset($old['role']) && strtoupper($old['role']) == 'EMPLOYEE') ? 'selected' : ''; ?>>Employee</option>
                            <option value="MANAGER" <?php echo (isset($old['role']) && strtoupper($old['role']) == 'MANAGER') ? 'selected' : ''; ?>>Manager</option>
                            <option value="HR" <?php echo (isset($old['role']) && strtoupper($old['role']) == 'HR') ? 'selected' : ''; ?>>HR</option>
                            <option value="ADMIN" <?php echo (isset($old['role']) && strtoupper($old['role']) == 'ADMIN') ? 'selected' : ''; ?>>Admin</option>
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

                <!-- Password -->
                <div class="form-group <?php echo isset($errors['password']) ? 'has-error' : ''; ?>">
                    <label for="password">Password <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="password" name="password" id="password"
                               placeholder="Min 6 characters">
                        <svg class="input-icon" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        <button type="button" class="pass-toggle" onclick="togglePassword('password', this)">
                            <svg viewBox="0 0 24 24" class="eye-open"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg viewBox="0 0 24 24" class="eye-closed" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <?php echo htmlspecialchars($errors['password']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Confirm Password -->
                <div class="form-group <?php echo isset($errors['confirm_password']) ? 'has-error' : ''; ?>">
                    <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="password" name="confirm_password" id="confirm_password"
                               placeholder="Re-enter password">
                        <svg class="input-icon" viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        <button type="button" class="pass-toggle" onclick="togglePassword('confirm_password', this)">
                            <svg viewBox="0 0 24 24" class="eye-open"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg viewBox="0 0 24 24" class="eye-closed" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                        </button>
                    </div>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <?php echo htmlspecialchars($errors['confirm_password']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Submit -->
                <div class="full-width">
                    <button type="submit" class="btn-submit">
                        Create Account
                    </button>
                </div>

            </div>
        </form>

        <div class="form-footer">
            <span>Already have an account? <a href="index.php?route=login">Sign in</a></span>
            &nbsp;·&nbsp;
            <span><a href="index.php?route=users">View all users</a></span>
            &nbsp;·&nbsp;
            <span><a href="index.php?route=users/bulk-create">Add Multiple Users</a></span>
        </div>

    </div>
</div>

<script>
    function togglePassword(fieldId, btn) {
        const input = document.getElementById(fieldId);
        const eyeOpen = btn.querySelector('.eye-open');
        const eyeClosed = btn.querySelector('.eye-closed');
        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.style.display = 'none';
            eyeClosed.style.display = 'block';
        } else {
            input.type = 'password';
            eyeOpen.style.display = 'block';
            eyeClosed.style.display = 'none';
        }
    }
</script>
</body>
</html>