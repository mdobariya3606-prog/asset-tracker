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
    <style>
        /* ========================= */
        /* Global Styles */
        /* ========================= */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

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
            width: 420px;
            height: 420px;
            background: radial-gradient(circle, #3b82f6 0%, transparent 70%);
            top: -120px;
            right: -100px;
        }

        body::after {
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, #06b6d4 0%, transparent 70%);
            bottom: -80px;
            left: -60px;
        }

        .edit-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 580px;
        }

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
            overflow: hidden;
        }

        .card-header .icon svg {
            width: 28px;
            height: 28px;
            fill: none;
            stroke: #fff;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .card-header .icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
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
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            stroke: #f87171;
            fill: none;
            stroke-width: 2;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

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
            width: 18px;
            height: 18px;
            stroke: #94a3b8;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: stroke 0.3s;
            pointer-events: none;
            z-index: 10;
        }

        .form-group input, .form-group select {
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

        .form-group input:focus, .form-group select:focus {
            border-color: #3b82f6;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        .form-group input:focus ~ .input-icon, .form-group select:focus ~ .input-icon {
            stroke: #3b82f6;
        }

        .form-group.has-error input, .form-group.has-error select {
            border-color: #f87171;
            background: rgba(248, 113, 113, 0.06);
        }

        .form-group.has-error input:focus, .form-group.has-error select:focus {
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
            width: 14px;
            height: 14px;
            flex-shrink: 0;
            stroke: #f87171;
            fill: none;
            stroke-width: 2;
        }

        .actions-row {
            grid-column: 1 / -1;
            display: flex;
            gap: 12px;
            margin-top: 12px;
        }

        .btn-submit {
            flex: 1;
            padding: 14px;
            background: #133458;
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
            background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            transform: translateX(-100%);
            transition: transform 0.5s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(19, 52, 88, 0.35);
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
            width: 18px;
            height: 18px;
            stroke: currentColor;
            fill: none;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
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

        @media (max-width: 600px) {
            .card {
                padding: 32px 24px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-grid .full-width {
                grid-column: auto;
            }

            .card-header h1 {
                font-size: 22px;
            }

            .actions-row {
                flex-direction: column-reverse;
            }
        }
    </style>
</head>
<body>
<!-- ========================= -->
<!-- Edit User Page -->
<!-- ========================= -->
<div class="edit-container<?php echo (!empty($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN') ? ' admin-edit' : ''; ?>">
    <div class="card">
        <!-- ========================= -->
        <!-- Card Header -->
        <!-- ========================= -->
        <div class="card-header">
            <div class="icon">
                <?php if (!empty($assetRequest['profile_image'])): ?>
                    <img src="../storage/profile_images/<?= htmlspecialchars($assetRequest['profile_image']) ?>"
                         alt="Profile image">
                <?php else: ?>
                    <svg viewBox="0 0 24 24">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 1 1 3 3L12 15l-4 1 1-4Z"/>
                    </svg>
                <?php endif; ?>
            </div>
            <h1>Edit Asset</h1>
            <p>Modify details for <?php echo htmlspecialchars($old['name'] ?? $assetRequest['name'] ?? 'User'); ?></p>
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
        <form action="index.php?route=assets/edit&id=<?= (int)($request['id'] ?? 0) ?>" method="post">
            <div class="form-grid">

                <!-- ========================= -->
                <!-- Name Field -->
                <!-- ========================= -->
                <div class="form-group  <?php echo isset($errors['name']) ? 'has-error' : ''; ?>">
                    <label for="name">Asset Name <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="name" id="name" placeholder="ex. Dell OptiPlex 7010"
                               value="<?php echo htmlspecialchars($old['name'] ?? $assetRequest['name'] ?? ''); ?>">
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
                <!-- Serial Number Field -->
                <!-- ========================= -->
                <div class="form-group  <?php echo isset($errors['serial_number']) ? 'has-error' : ''; ?>">
                    <label for="name">Serial Number <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" id="serial_number" name="serial_number" placeholder="ex. DOPT7010SN15"
                               value="<?= htmlspecialchars($old['serial_number'] ?? $assetRequest['serial_number'] ?? '') ?>">

                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M20 7 12 3 4 7v10l8 4 8-4V7z"/>
                            <path d="M12 21V11"/>
                            <path d="M4 7l8 4 8-4"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['serial_number'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['serial_number']); ?>
                        </div>
                    <?php endif; ?>
                </div>


                <!-- ========================= -->
                <!-- Brand Field -->
                <!-- ========================= -->
                <div class="form-group <?php echo isset($errors['brand']) ? 'has-error' : ''; ?>">
                    <label for="email">Brand <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="brand" id="brand" placeholder="ex. Dell"
                               value="<?php echo htmlspecialchars($old['brand'] ?? $assetRequest['brand'] ?? ''); ?>">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="6"/>
                            <path d="M8.5 14 7 22l5-3 5 3-1.5-8"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['brand'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['brand']); ?>
                        </div>
                    <?php endif; ?>
                </div>
                <!-- ========================= -->
                <!-- Model Field -->
                <!-- ========================= -->
                <div class="form-group <?php echo isset($errors['model']) ? 'has-error' : ''; ?>">
                    <label for="model">Model <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="model" id="model" placeholder="ex. OptiPlex 7010"
                               value="<?php echo htmlspecialchars($old['model'] ?? $assetRequest['model'] ?? ''); ?>">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <rect x="7" y="7" width="10" height="10" rx="1"/>
                            <path d="M9 1v3M15 1v3M9 20v3M15 20v3"/>
                            <path d="M20 9h3M20 15h3M1 9h3M1 15h3"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['model'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg><?php echo htmlspecialchars($errors['model']); ?></div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Category Selection -->
                <!-- ========================= -->
                <div class="form-group <?php echo isset($errors['category_id']) ? 'has-error' : ''; ?>">
                    <label for="category_id">Category <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select id="category_id" name="category_id">
                            <option value="">Select Category</option>
                            <?php $selectedCategory = (int)($assetRequest['category_id'] ?? 0); ?>
                            <?php foreach ($categories ?? [] as $category): ?>
                                <option value="<?= (int)$category['id'] ?>" <?= $selectedCategory === (int)$category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['category_id'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg><?php echo htmlspecialchars($errors['category_id']); ?></div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Vendor Selection -->
                <!-- ========================= -->
                <div class="form-group <?php echo isset($errors['vendor_id']) ? 'has-error' : ''; ?>">
                    <label for="vendor_id">Vendor <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select id="vendor_id" name="vendor_id">
                            <option value="">Select Vendor</option>
                            <?php $selectedVendor = (int)($assetRequest['vendor_id'] ?? 0); ?>
                            <?php foreach ($vendors ?? [] as $vendor): ?>
                                <option value="<?= (int)$vendor['id'] ?>" <?= $selectedVendor === (int)$vendor['id'] ? 'selected' : '' ?>><?= htmlspecialchars($vendor['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M3 9l1-5h16l1 5"/>
                            <path d="M4 9v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9"/>
                            <path d="M9 22V12h6v10"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['vendor_id'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg><?php echo htmlspecialchars($errors['vendor_id']); ?></div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Status Selection -->
                <!-- ========================= -->

                <div class="form-group <?php echo isset($errors['designation_id']) ? 'has-error' : ''; ?>">
                    <label for="status">Status <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <select name="status" id="status">
                            <option value="">Select Status</option>
                            <?php $currentStat = $assetRequest['status'] ?? 'AVAILABLE'; ?>
                            <?php foreach ($statusEnum ?? [] as $status): ?>
                                <option value="<?php echo strtoupper($status) ?>" <?php echo($currentStat == strtoupper($status) ? 'selected' : ''); ?>><?php echo htmlspecialchars($status); ?></option>
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
                <!-- Cost Field -->
                <!-- ========================= -->
                <div class="form-group <?php echo isset($errors['mobile']) ? 'has-error' : ''; ?>">
                    <label for="mobile">Cost <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="text" name="cost" id="cost" placeholder="ex. 48000" maxlength="10"
                               value="<?php echo htmlspecialchars($old['cost'] ?? $assetRequest['cost'] ?? ''); ?>">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <path d="M20.59 13.41L11 3H4v7l9.59 9.59a2 2 0 0 0 2.82 0l4.18-4.18a2 2 0 0 0 0-2.82z"/>
                            <circle cx="7.5" cy="7.5" r="1.5"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['cost'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['cost']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Purchase Date Field -->
                <!-- ========================= -->
                <div class="form-group <?php echo isset($errors['mobile']) ? 'has-error' : ''; ?>">
                    <label for="mobile">Purchase Date <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="date" id="purchase_date" name="purchase_date"
                               value="<?= htmlspecialchars($old['purchase_date'] ?? $assetRequest['purchase_date'] ?? '') ?>">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['purchase_date'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['purchase_date']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Warranty Date Field -->
                <!-- ========================= -->
                <div class="form-group <?php echo isset($errors['mobile']) ? 'has-error' : ''; ?>">
                    <label for="mobile">Warranty Date <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="date" id="warranty_date" name="warranty_date"
                               value="<?= htmlspecialchars($old['warranty_date'] ?? $assetRequest['warranty_date'] ?? '') ?>">
                        <svg class="input-icon" viewBox="0 0 24 24">
                            <rect x="3" y="4" width="18" height="18" rx="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </div>
                    <?php if (isset($errors['warranty_date'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['warranty_date']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Profile Image Upload -->
                <!-- ========================= -->
                <div class="form-group full-width <?php echo isset($errors['profile_image']) ? 'has-error' : ''; ?>">
                    <label for="profile_image">Profile Image (optional)</label>
                    <div class="input-wrapper">
                        <input type="file" name="profile_image" id="profile_image" accept=".png,.jpg,.jpeg,.webp">
                    </div>
                    <?php if (!empty($assetRequest['profile_image'])): ?>
                        <div class="error-text" style="margin-top: 10px; display: block; color: #475569;">
                            Current image:
                            <strong><?php echo htmlspecialchars($assetRequest['profile_image']); ?></strong>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($errors['profile_image'])): ?>
                        <div class="error-text">
                            <svg viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <?php echo htmlspecialchars($errors['profile_image']); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ========================= -->
                <!-- Form Actions -->
                <!-- Cancel & Save Buttons -->
                <!-- ========================= -->
                <div class="actions-row">
                    <a href="index.php?route=assets/show&id=<?= $request['id']; ?>" class="btn-cancel">Cancel</a>
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