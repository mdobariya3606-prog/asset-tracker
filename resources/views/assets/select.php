<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/style.css">
</head>
<body>
<div class="page">
    <?php include '../resources/views/layouts/header.php'; ?>

    <nav class="admin-tabs">
        <a href="index.php?route=users" class="tab-link">Users</a>
        <a href="index.php?route=departments" class="tab-link">Departments</a>
        <a href="index.php?route=designations" class="tab-link">Designations</a>
        <a href="index.php?route=assets" class="tab-link active">Assets</a>
        <a href="index.php?route=requests" class="tab-link">Requests</a>
    </nav>

    <div class="page-header">
        <div>
            <h2>Assets</h2>
            <p>Manage office assets, availability, and requests from one place.</p>
        </div>
        <?php if ($canManageAssets ?? false): ?>
            <a class="btn btn-primary" href="index.php?route=assets/create">
                <svg viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Asset</a>
        <?php endif; ?>
    </div>


    <!-- Success Message Banner -->
    <?php
    if (isset($_SESSION['success'])): ?>
        <div class="alert-success">
            <svg viewBox="0 0 24 24">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                <polyline points="22 4 12 14.01 9 11.01"/>
            </svg>
            <div><?= htmlspecialchars($_SESSION['success']) ?></div>
        </div>
        <?php unset($_SESSION['success']);
    endif;
    ?>

    <!-- Error Message Banner -->
    <?php
    if (isset($_SESSION['general'])): ?>
        <div class="alert-error">
            <svg viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <div><?= htmlspecialchars($_SESSION['general']) ?></div>
        </div>
        <?php unset($_SESSION['general']);
    endif;
    ?>

    <div class="card">
        <table>
            <thead>
            <tr>
                <th>Asset ID</th>
                <th>Asset Name</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($assets ?? [])): ?>
                <tr>
                    <td colspan="3" class="empty-state">No assets found yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($assets ?? [] as $asset): ?>
                    <tr>
                        <td style="color: var(--slate-400); font-weight: 600;">
                            #<?= htmlspecialchars($asset['id'] ?? '') ?></td>
                        <td><a class="asset-name-link"
                               href="index.php?route=assets/show&id=<?= (int)($asset['id'] ?? 0) ?>"><?= htmlspecialchars($asset['name'] ?? '') ?></a>
                        </td>
                        <td>
                            <?php $status = strtolower((string)($asset['status'] ?? '')); ?>
                            <span class="pill pill-<?= htmlspecialchars($status === '' ? 'available' : $status) ?>"><?= htmlspecialchars($asset['status'] ?? '') ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>