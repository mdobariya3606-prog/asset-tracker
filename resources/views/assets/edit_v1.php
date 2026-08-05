<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Asset — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --blue: #3b82f6;
            --blue-dark: #2563eb;
            --accent: #133458;
            --accent-dark: #133458;
            --cyan: #06b6d4;
            --green: #10b981;
            --red: #ef4444;
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-400: #94a3b8;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50: #f8fafc;
            --white: #ffffff;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, .08);
            --shadow-md: 0 4px 16px rgba(0, 0, 0, .08);
            --radius-sm: 10px;
            --radius-md: 14px;
            --radius-lg: 20px;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: var(--slate-100);
            padding: 40px 20px 80px;
            color: var(--slate-800);
        }

        .page {
            max-width: 1100px;
            margin: 0 auto;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-md);
            padding: 16px 24px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 32px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--blue), var(--cyan));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(59, 130, 246, .25);
        }

        .logo-icon svg {
            width: 20px;
            height: 20px;
            stroke: #fff;
            fill: none;
            stroke-width: 2;
        }

        .logo-text h1 {
            font-size: 18px;
            font-weight: 700;
            color: var(--slate-900);
            letter-spacing: -.3px;
        }

        .logo-text span {
            font-size: 11px;
            color: var(--slate-400);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-greeting {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--slate-600);
            font-weight: 500;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 600;
            background: var(--slate-100);
            color: var(--slate-700);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all .25s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            box-shadow: 0 2px 10px rgba(19, 52, 88, .3);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(19, 52, 88, .4);
        }

        .btn-secondary {
            background: var(--slate-100);
            color: var(--slate-700);
        }

        .btn-secondary:hover {
            background: var(--slate-200);
        }

        .btn-logout {
            background: #fef2f2;
            color: var(--red);
            border: 1.5px solid #fecaca;
        }

        .btn-logout:hover {
            background: #fee2e2;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 24px;
        }

        .page-header h2 {
            font-size: 24px;
            font-weight: 700;
            color: var(--slate-900);
            letter-spacing: -.5px;
        }

        .page-header p {
            font-size: 14px;
            color: var(--slate-500);
            margin-top: 2px;
        }

        .admin-tabs {
            display: flex;
            gap: 24px;
            margin: 0 0 28px 0;
            border-bottom: 1.5px solid var(--slate-200);
            padding-bottom: 0;
        }

        .tab-link {
            padding: 10px 4px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            color: var(--slate-500);
            border-bottom: 3.5px solid transparent;
            margin-bottom: -1.5px;
            transition: all 0.2s ease;
        }

        .tab-link:hover {
            color: var(--slate-800);
        }

        .tab-link.active {
            color: var(--blue);
            border-color: var(--blue);
        }

        .card {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            overflow: hidden;
            padding: 28px;
        }

        .card-header {
            margin-bottom: 24px;
        }

        .card-header h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--slate-900);
        }

        .card-header p {
            font-size: 14px;
            color: var(--slate-500);
            margin-top: 4px;
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .alert-danger ul {
            margin: 8px 0 0 20px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-700);
        }

        input, select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--slate-200);
            border-radius: 10px;
            font: inherit;
            color: var(--slate-800);
            background: var(--white);
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .12);
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
        }

        @media (max-width: 740px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="page">
    <header class="navbar">
        <div class="logo-section">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <path d="M7 8h10"/>
                    <path d="M7 12h7"/>
                    <path d="M7 16h4"/>
                </svg>
            </div>
            <div class="logo-text">
                <h1>AssetTracker</h1>
                <span>Asset Management</span>
            </div>
        </div>
        <div class="nav-links">
            <div class="user-greeting">
                <span class="badge"><?= htmlspecialchars($_SESSION['user_role'] ?? 'USER') ?></span>
                <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></span>
            </div>
            <a href="index.php?route=users" class="btn btn-secondary">Back to Dashboard</a>
            <a href="index.php?route=logout" class="btn btn-logout">Sign out</a>
        </div>
    </header>

    <div class="page-header">
        <div>
            <h2>Edit Asset</h2>
            <p>Update the asset details below and keep the information accurate.</p>
        </div>
        <a href="index.php?route=assets" class="btn btn-secondary">Back to Assets</a>
    </div>

    <nav class="admin-tabs">
        <a href="index.php?route=users" class="tab-link">Users</a>
        <a href="index.php?route=departments" class="tab-link">Departments</a>
        <a href="index.php?route=designations" class="tab-link">Designations</a>
        <a href="index.php?route=assets" class="tab-link active">Assets</a>
    </nav>

    <div class="card">
        <div class="card-header">
            <h3>Asset Details</h3>
            <p>All fields are required before the asset can be saved.</p>
        </div>

		<?php $errors = $errors ?? [];
		$assetData = $assetData ?? $asset ?? []; ?>
		<?php if (!empty($errors)): ?>
            <div class="alert-danger">
                <strong>Please correct the following:</strong>
                <ul>
					<?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
					<?php endforeach; ?>
                </ul>
            </div>
		<?php endif; ?>

        <form action="index.php?route=assets/edit&id=<?= (int)($asset['id'] ?? 0) ?>" method="post">
            <div class="form-grid">
                <div class="field">
                    <label for="name">Asset Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($assetData['name'] ?? '') ?>"
                           required>
                </div>
                <div class="field">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        <option value="">Select Category</option>
						<?php $selectedCategory = (int)($assetData['category_id'] ?? 0); ?>
						<?php foreach ($categories ?? [] as $category): ?>
                            <option value="<?= (int)$category['id'] ?>" <?= $selectedCategory === (int)$category['id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
						<?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="brand">Brand</label>
                    <input type="text" id="brand" name="brand"
                           value="<?= htmlspecialchars($assetData['brand'] ?? '') ?>" required>
                </div>
                <div class="field">
                    <label for="model">Model</label>
                    <input type="text" id="model" name="model"
                           value="<?= htmlspecialchars($assetData['model'] ?? '') ?>" required>
                </div>
                <div class="field">
                    <label for="serial_number">Serial Number</label>
                    <input type="text" id="serial_number" name="serial_number"
                           value="<?= htmlspecialchars($assetData['serial_number'] ?? '') ?>" required>
                </div>
                <div class="field">
                    <label for="purchase_date">Purchase Date</label>
                    <input type="date" id="purchase_date" name="purchase_date"
                           value="<?= htmlspecialchars($assetData['purchase_date'] ?? '') ?>" required>
                </div>
                <div class="field">
                    <label for="warranty_date">Warranty Date</label>
                    <input type="date" id="warranty_date" name="warranty_date"
                           value="<?= htmlspecialchars($assetData['warranty_date'] ?? '') ?>" required>
                </div>

                <div class="field">
                    <label for="vendor_id">Vendor</label>
                    <select id="vendor_id" name="vendor_id" required>
                        <option value="">Select Vendor</option>
						<?php $selectedVendor = (int)($assetData['vendor_id'] ?? 0); ?>
						<?php foreach ($vendors ?? [] as $vendor): ?>
                            <option value="<?= (int)$vendor['id'] ?>" <?= $selectedVendor === (int)$vendor['id'] ? 'selected' : '' ?>><?= htmlspecialchars($vendor['name']) ?></option>
						<?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <label for="cost">Cost</label>
                    <input type="number" step="0.01" min="0" id="cost" name="cost"
                           value="<?= htmlspecialchars($assetData['cost'] ?? '') ?>" required>
                </div>
                <div class="field full">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
						<?php $selectedStatus = strtoupper((string)($assetData['status'] ?? 'AVAILABLE')); ?>
                        <option value="AVAILABLE" <?= $selectedStatus === 'AVAILABLE' ? 'selected' : '' ?>>Available
                        </option>
                        <option value="ASSIGNED" <?= $selectedStatus === 'ASSIGNED' ? 'selected' : '' ?>>Assigned
                        </option>
                        <option value="REPAIR" <?= $selectedStatus === 'REPAIR' ? 'selected' : '' ?>>Repair</option>
                        <option value="LOST" <?= $selectedStatus === 'LOST' ? 'selected' : '' ?>>Lost</option>
                        <option value="SCRAP" <?= $selectedStatus === 'SCRAP' ? 'selected' : '' ?>>Scrap</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <a href="index.php?route=assets" class="btn btn-secondary">Cancel</a>
                <button class="btn btn-primary" type="submit">Update Asset</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>