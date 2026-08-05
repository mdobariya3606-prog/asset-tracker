<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/style.css">
</head>
<body>
<div class="page">

    <?php include '../resources/views/layouts/header.php'; ?>

    <nav class="admin-tabs">
        <a href="index.php?route=users" class="tab-link">Users</a>
        <a href="index.php?route=departments" class="tab-link active">Departments</a>
        <a href="index.php?route=designations" class="tab-link">Designations</a>
        <a href="index.php?route=assets" class="tab-link">Assets</a>
        <a href="index.php?route=requests" class="tab-link">Requests</a>
    </nav>

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

    <div class="page-header">
        <div>
            <h2>Departments</h2>
            <p>Configure organizational team hierarchy and departments</p>
        </div>
        <?php if (($role ?? 'EMPLOYEE') == 'ADMIN'): ?>
            <a href="index.php?route=departments/create" class="btn btn-primary">
                <svg viewBox="0 0 24 24"
                     style="width:16px; height:16px; fill:none; stroke:currentColor; stroke-width:2.5;">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Department
            </a>
        <?php endif; ?>
    </div>

    <div class="card">
        <?php if (empty($departments)): ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                    <line x1="9" y1="3" x2="9" y2="21"/>
                </svg>
                <h3>No departments registered yet.</h3>
            </div>
        <?php else: ?>
            <table>
                <thead>
                <tr>
                    <th style="width: 100px;">ID</th>
                    <th>Department Name</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($departments as $dept): ?>
                    <tr>
                        <td style="color: var(--slate-400); font-weight: 600;">
                            #<?= htmlspecialchars($dept['id']) ?></td>
                        <td><a href="index.php?route=users&department_id=<?= $dept['id'] ?>"
                               style="color: var(--blue); text-decoration: none; font-weight: 600;"><?= htmlspecialchars($dept['name']) ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>