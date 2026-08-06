<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/style.css">
    <link rel="stylesheet" href="../resources/css/user.css">
</head>
<body>
<div class="page">
    <?php include '../resources/views/layouts/header.php'; ?>

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

    <div class="card">
        <table>
            <thead>
            <tr>
                <th>Request ID</th>
                <th>User Id</th>
                <th>Asset Id</th>
                <th>Status</th>
                <th>Requested At</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($requests ?? [])): ?>
                <tr>
                    <td colspan="3" class="empty-state">No assets found yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($requests ?? [] as $request): ?>
                    <tr>
                        <td style="color: var(--slate-400); font-weight: 600;">
                            <a href="index.php?route=assets/requests/show&id=<?= $request['id'] ?>"
                               style="color: var(--slate-500); font-weight: 600;"
                               onclick="<?php $_SESSION['back'] = 'index.php?route=assets/requests'; ?>">
                                #<?= htmlspecialchars($request['id'] ?? '') ?>
                            </a>
                        <td>
                            <a href="index.php?route=users/profile&id=<?= $request['user_id'] ?>"
                               style="color: var(--slate-500); font-weight: 600;"
                               onclick="<?php $_SESSION['back'] = 'index.php?route=assets/requests'; ?>">
                                #<?= htmlspecialchars($request['user_id'] ?? '') ?>
                            </a>
                        </td>

                        <td>
                            <a href="index.php?route=assets/show&id=<?= $request['asset_id'] ?>"
                               style="color: var(--slate-500); font-weight: 600;"
                               onclick="<?php $_SESSION['back'] = 'index.php?route=assets/requests'; ?>">
                                #<?= htmlspecialchars($request['asset_id'] ?? '') ?>
                            </a>
                        </td>

                        <td class="asset-status">
                                    <span class="badge badge-<?= strtolower($request['status']) ?>">
                                    <?= htmlspecialchars($request['status'] ?? 'N/A') ?>
                                </span>
                        </td>

                        <td style="color: var(--slate-500); font-weight: 600;">
                            <?= htmlspecialchars($request['requested_at'] ?? '') ?>
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