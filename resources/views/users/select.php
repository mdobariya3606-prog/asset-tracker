<?php
function getSortUrl(string $column, string $currentSort, string $currentOrder, string $search, int $page, ?int $deptId = null, ?int $desigId = null): string {
    $newOrder = ($currentSort === $column && $currentOrder === 'asc') ? 'desc' : 'asc';
    $url = "index.php?route=users&search=" . urlencode($search) . "&page={$page}&sort={$column}&order={$newOrder}";
    if ($deptId !== null) {
        $url .= "&department_id={$deptId}";
    }
    if ($desigId !== null) {
        $url .= "&designation_id={$desigId}";
    }
    return $url;
}

function getSortIndicator(string $column, string $currentSort, string $currentOrder): string {
    if ($currentSort !== $column) {
        return ' <span style="opacity: 0.35; font-size: 10px; font-weight: normal;">▲▼</span>';
    }
    return $currentOrder === 'asc' 
        ? ' <span style="color: var(--blue); font-size: 11px;">▲</span>' 
        : ' <span style="color: var(--blue); font-size: 11px;">▼</span>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Dashboard — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue:      #3b82f6;
            --blue-dark: #2563eb;
            --cyan:      #06b6d4;
            --green:     #10b981;
            --red:       #ef4444;
            --slate-900: #0f172a;
            --slate-800: #1e293b;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-500: #64748b;
            --slate-400: #94a3b8;
            --slate-200: #e2e8f0;
            --slate-100: #f1f5f9;
            --slate-50:  #f8fafc;
            --white:     #ffffff;
            --shadow-sm: 0 1px 3px rgba(0,0,0,.08);
            --shadow-md: 0 4px 16px rgba(0,0,0,.08);
            --shadow-lg: 0 8px 32px rgba(0,0,0,.10);
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
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ── Navigation / Header ── */
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
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--blue), var(--cyan));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(59,130,246,.25);
        }
        .logo-icon svg {
            width: 20px; height: 20px; stroke: #fff; fill: none; stroke-width: 2;
        }

        .logo-text h1 {
            font-size: 18px; font-weight: 700; color: var(--slate-900); letter-spacing: -.3px;
        }
        .logo-text span {
            font-size: 11px; color: var(--slate-400); font-weight: 500; text-transform: uppercase; letter-spacing: .5px;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .avatar-badge {
            width: 36px; height: 36px;
            background: var(--slate-100);
            border: 1.5px solid var(--slate-200);
            color: var(--slate-700);
            font-weight: 600; font-size: 13px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }

        /* ── Page Title ── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 24px;
        }

        .page-header h2 {
            font-size: 24px; font-weight: 700; color: var(--slate-900); letter-spacing: -.5px;
        }
        .page-header p {
            color: var(--slate-500); font-size: 14px; margin-top: 2px;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        /* ── Alert Banner ── */
        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            border-radius: var(--radius-md);
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
            width: 20px; height: 20px; stroke: #34d399; fill: none; stroke-width: 2;
        }

        /* ── Search Bar / Filters ── */
        .search-container {
            position: relative;
            max-width: 320px;
            margin-bottom: 20px;
        }
        .search-container input {
            width: 100%;
            padding: 10px 16px 10px 38px;
            background: var(--white);
            border: 1.5px solid var(--slate-200);
            border-radius: var(--radius-sm);
            font-family: inherit; font-size: 13px;
            color: var(--slate-800); outline: none;
            transition: all .2s;
        }
        .search-container input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(59,130,246,.1);
        }
        .search-container svg {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            width: 16px; height: 16px; stroke: var(--slate-400); fill: none; stroke-width: 2;
        }

        /* ── Table Styling ── */
        .card {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th, td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--slate-200);
        }

        th {
            background: var(--slate-50);
            font-size: 12px;
            font-weight: 600;
            color: var(--slate-600);
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .user-id {
            font-size: 13px; font-weight: 600; color: var(--slate-400);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(6, 182, 212, 0.1));
            color: var(--blue);
            border-radius: 50%;
            font-size: 12px; font-weight: 600;
            display: flex; align-items: center; justify-content: center;
        }

        .user-name {
            font-weight: 600; color: var(--slate-900); font-size: 14px;
        }

        .user-email {
            font-size: 13px; color: var(--slate-400);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 500;
        }
        .badge-dept {
            background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;
        }
        .badge-desig {
            background: #faf5ff; color: #6b21a8; border: 1px solid #e9d5ff;
        }
        .badge-role-admin {
            background: #fef2f2; color: #991b1b; border: 1px solid #fecaca;
        }
        .badge-role-hr {
            background: #fdf2f8; color: #9d174d; border: 1px solid #fbcfe8;
        }
        .badge-role-manager {
            background: #fff7ed; color: #9a3412; border: 1px solid #ffedd5;
        }
        .badge-role-employee {
            background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;
        }

        /* ── Buttons ── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-family: inherit; font-size: 13px; font-weight: 600;
            text-decoration: none; cursor: pointer;
            transition: all .25s ease;
            border: none;
        }
        .btn svg {
            width: 16px; height: 16px; stroke: currentColor; fill: none; stroke-width: 2;
            stroke-linecap: round; stroke-linejoin: round;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--blue), var(--blue-dark));
            color: #white; color: #fff;
            box-shadow: 0 2px 10px rgba(59,130,246,.3);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(59,130,246,.4);
        }

        .btn-secondary {
            background: var(--white);
            color: var(--blue);
            border: 1.5px solid #bfdbfe;
        }
        .btn-secondary:hover {
            background: #eff6ff;
        }

        .btn-logout {
            background: #fef2f2;
            color: var(--red);
            border: 1.5px solid #fecaca;
        }
        .btn-logout:hover {
            background: #fee2e2;
        }

        /* ── Pagination Styling ── */
        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 20px 24px 20px;
            border-top: 1px solid var(--slate-100);
            flex-wrap: wrap;
            gap: 16px;
        }

        .pagination-info {
            font-size: 13px;
            color: var(--slate-500);
        }

        .pagination-info strong {
            color: var(--slate-800);
            font-weight: 600;
        }

        .pagination-list {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pagination-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            font-size: 13px;
            font-weight: 500;
            color: var(--slate-600);
            background-color: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-sm);
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .pagination-link:hover {
            border-color: var(--slate-300);
            color: var(--slate-800);
            background-color: var(--slate-50);
        }

        .pagination-link.active {
            background-color: var(--blue);
            color: var(--white);
            border-color: var(--blue);
            font-weight: 600;
            cursor: default;
            pointer-events: none;
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.2);
        }

        /* ── Tabs Styling ── */
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

        /* ── Empty State ── */
        .empty-state {
            text-align: center;
            padding: 60px 24px;
        }
        .empty-state svg {
            width: 64px; height: 64px; stroke: var(--slate-400); stroke-width: 1.5; margin-bottom: 16px;
        }
        .empty-state h3 {
            font-size: 18px; font-weight: 600; color: var(--slate-800); margin-bottom: 6px;
        }
        .empty-state p {
            color: var(--slate-500); font-size: 14px;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .navbar { flex-direction: column; gap: 16px; text-align: center; }
            .nav-user { width: 100%; justify-content: center; }
            .page-header { flex-direction: column; align-items: flex-start; }
            .header-actions { width: 100%; justify-content: flex-start; }
            th, td { padding: 12px 14px; }
        }
    </style>
</head>
<body>

<div class="page">

    <!-- Navbar -->
    <header class="navbar">
        <div class="logo-section">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <div class="logo-text">
                <h1>AssetTracker</h1>
                <span>System Administration</span>
            </div>
        </div>

        <div class="nav-user">
            <?php if (!empty($dashboardUser)): ?>
                <div class="avatar-badge">
                    <?= strtoupper(substr($dashboardUser['name'], 0, 1)) ?>
                </div>
                <div style="text-align: left; line-height: 1.2;">
                    <div style="font-weight: 600; font-size: 13px; color: var(--slate-800);"><?= htmlspecialchars($dashboardUser['name']) ?></div>
                    <div style="font-size: 11px; color: var(--slate-500);"><?= htmlspecialchars($dashboardUser['email']) ?></div>
                </div>
                <a href="index.php?route=users/edit&id=<?= (int) $dashboardUser['id'] ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg>
                    Profile
                </a>
                <a href="index.php?route=logout" class="btn btn-logout" style="padding: 6px 12px; font-size: 12px;">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Sign out
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Admin Navigation Tabs -->
    <?php if ($dashboardUserRole === 'ADMIN'): ?>
        <nav class="admin-tabs">
            <a href="index.php?route=users" class="tab-link active">Users</a>
            <a href="index.php?route=departments" class="tab-link">Departments</a>
            <a href="index.php?route=designations" class="tab-link">Designations</a>
        </nav>
    <?php endif; ?>

    <!-- Success Message Banner -->
    <?php if (isset($success)): ?>
        <div class="alert-success">
            <svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <div><?= htmlspecialchars($success) ?></div>
        </div>
    <?php endif; ?>

    <!-- Header Actions -->
    <div class="page-header">
        <div>
            <h2>Users Directory</h2>
            <p>Manage, inspect, and register team members inside the company</p>
        </div>
        <?php if ($dashboardUserRole !== 'EMPLOYEE'): ?>
        <div class="header-actions">
            <a href="index.php?route=users/create" class="btn btn-primary">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Register User
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Search Box -->
    <form action="index.php" method="get" class="search-container" id="searchForm">
        <input type="hidden" name="route" value="users">
        <?php if ($departmentId !== null): ?>
            <input type="hidden" name="department_id" value="<?= htmlspecialchars($departmentId) ?>">
        <?php endif; ?>
        <?php if ($designationId !== null): ?>
            <input type="hidden" name="designation_id" value="<?= htmlspecialchars($designationId) ?>">
        <?php endif; ?>
        <input type="text" name="search" id="searchInput" placeholder="Search by name, email, department..." value="<?= htmlspecialchars($search ?? '') ?>">
        <svg viewBox="0 0 24 24" style="cursor: pointer;" onclick="document.getElementById('searchForm').submit();"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    </form>

    <!-- Active Filters Feedback Section -->
    <?php if ($departmentId !== null || $designationId !== null): ?>
        <div class="filter-status" style="display: flex; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 24px; padding: 2px 4px;">
            <span style="font-size: 13px; color: var(--slate-500); font-weight: 600; margin-right: 4px;">Active Filters:</span>
            <?php if ($departmentId !== null && isset($activeDeptName)): ?>
                <span class="badge" style="background: #eff6ff; color: var(--blue); padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid rgba(59, 130, 246, 0.2); display: inline-flex; align-items: center; gap: 8px;">
                    Department: <?= htmlspecialchars($activeDeptName) ?>
                    <a href="index.php?route=users&search=<?= urlencode($search) ?><?php if ($designationId !== null) echo '&designation_id=' . $designationId; ?>" style="color: var(--blue); text-decoration: none; font-size: 15px; font-weight: bold; line-height: 1; cursor: pointer;">&times;</a>
                </span>
            <?php endif; ?>
            <?php if ($designationId !== null && isset($activeDesigName)): ?>
                <span class="badge" style="background: #ecfdf5; color: #10b981; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.2); display: inline-flex; align-items: center; gap: 8px;">
                    Designation: <?= htmlspecialchars($activeDesigName) ?>
                    <a href="index.php?route=users&search=<?= urlencode($search) ?><?php if ($departmentId !== null) echo '&department_id=' . $departmentId; ?>" style="color: #10b981; text-decoration: none; font-size: 15px; font-weight: bold; line-height: 1; cursor: pointer;">&times;</a>
                </span>
            <?php endif; ?>
            <a href="index.php?route=users" style="font-size: 12px; color: var(--slate-400); text-decoration: none; font-weight: 600; margin-left: 8px; transition: color 0.15s ease;" onmouseover="this.style.color='var(--slate-700)'" onmouseout="this.style.color='var(--slate-400)'">
                Clear Filters
            </a>
        </div>
    <?php endif; ?>

    <!-- Listings Card -->
    <div class="card">
        <?php if (empty($users)): ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                <h3><?= isset($message) ? htmlspecialchars($message) : 'No Users Registered Yet' ?></h3>
                <p><?= ($search ?? '') !== '' ? 'Try clearing the search filter or using different keywords.' : 'Register a single user or add multiple users to populate the list.' ?></p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="usersTable">
                    <thead>
                        <tr>
                            <th style="width: 100px;">
                                <a href="<?= getSortUrl('id', $sort, $order, $search, $page, $departmentId, $designationId) ?>" style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                    ID<?= getSortIndicator('id', $sort, $order) ?>
                                </a>
                            </th>
                            <th>
                                <a href="<?= getSortUrl('name', $sort, $order, $search, $page, $departmentId, $designationId) ?>" style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                    User Name<?= getSortIndicator('name', $sort, $order) ?>
                                </a>
                            </th>
                            <th>
                                <a href="<?= getSortUrl('mobile', $sort, $order, $search, $page, $departmentId, $designationId) ?>" style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                    Contact / Mobile<?= getSortIndicator('mobile', $sort, $order) ?>
                                </a>
                            </th>
                            <th>
                                <a href="<?= getSortUrl('department', $sort, $order, $search, $page, $departmentId, $designationId) ?>" style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                    Department<?= getSortIndicator('department', $sort, $order) ?>
                                </a>
                            </th>
                            <th>
                                <a href="<?= getSortUrl('designation', $sort, $order, $search, $page, $departmentId, $designationId) ?>" style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                    Designation<?= getSortIndicator('designation', $sort, $order) ?>
                                </a>
                            </th>
                            <th>
                                <a href="<?= getSortUrl('role', $sort, $order, $search, $page, $departmentId, $designationId) ?>" style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                    Role<?= getSortIndicator('role', $sort, $order) ?>
                                </a>
                            </th>
                            <?php if ($dashboardUserRole === 'ADMIN'): ?>
                                <th style="text-align: right; width: 140px;">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr class="user-row-item">
                            <td class="user-id">#<?= $user['id'] ?></td>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="user-name"><?= htmlspecialchars($user['name']) ?></div>
                                        <div class="user-email"><?= htmlspecialchars($user['email']) ?></div>
                                    </div>
                                </div>
                            </td>
                            <td style="font-size: 13px; color: var(--slate-700); font-weight: 500;">
                                <?= htmlspecialchars($user['mobile']) ?>
                            </td>
                            <td>
                                <span class="badge badge-dept">
                                    <?= htmlspecialchars($user['department_name'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-desig">
                                    <?= htmlspecialchars($user['designation_name'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-role-<?= strtolower($user['role'] ?? 'employee') ?>">
                                    <?= htmlspecialchars($user['role'] ?? 'EMPLOYEE') ?>
                                </span>
                            </td>
                            <?php if ($dashboardUserRole === 'ADMIN'): ?>
                                <td style="text-align: right;">
                                    <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                        <a href="index.php?route=users/edit&id=<?= $user['id'] ?>" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px; min-height: auto;">
                                            Edit
                                        </a>
                                        <a href="index.php?route=users/delete&id=<?= $user['id'] ?>" class="btn btn-logout" style="padding: 6px 12px; font-size: 12px; min-height: auto;" onclick="return confirm('Are you sure you want to delete this user?');">
                                            Delete
                                        </a>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Controls -->
            <?php if (isset($totalPages) && $totalPages > 1): ?>
                <div class="pagination-container">
                    <div class="pagination-info">
                        Showing page <strong><?= $page ?></strong> of <strong><?= $totalPages ?></strong> (<strong><?= $totalUsers ?></strong> total users)
                    </div>
                    <div class="pagination-list">
                        <?php if ($page > 1): ?>
                            <a href="index.php?route=users&page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>&order=<?= urlencode($order) ?><?php if ($departmentId !== null) echo '&department_id=' . $departmentId; ?><?php if ($designationId !== null) echo '&designation_id=' . $designationId; ?>" class="pagination-link">
                                &laquo; Prev
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="index.php?route=users&page=<?= $i ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>&order=<?= urlencode($order) ?><?php if ($departmentId !== null) echo '&department_id=' . $departmentId; ?><?php if ($designationId !== null) echo '&designation_id=' . $designationId; ?>" 
                               class="pagination-link <?= $i === $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="index.php?route=users&page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>&order=<?= urlencode($order) ?><?php if ($departmentId !== null) echo '&department_id=' . $departmentId; ?><?php if ($designationId !== null) echo '&designation_id=' . $designationId; ?>" class="pagination-link">
                                Next &raquo;
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const searchInput = document.getElementById('searchInput');
    let debounceTimer;

    if (searchForm && searchInput) {
        // Prevent default submit to keep input focus and load dynamically
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            clearTimeout(debounceTimer);
            performSearch(searchInput.value);
        });

        // Trigger dynamic search as the user types (with 400ms debounce)
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            const query = this.value;
            debounceTimer = setTimeout(() => {
                performSearch(query);
            }, 400);
        });
    }

    // Intercept clicks on links inside the card (sorting and paging)
    const cardElement = document.querySelector('.card');
    if (cardElement) {
        cardElement.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            // Dynamically load only the users list links. Edit links must perform
            // a full page navigation so their page-specific stylesheet is applied.
            const route = link?.href ? new URL(link.href).searchParams.get('route') : null;
            if (link && route === 'users' && !link.getAttribute('onclick')) {
                e.preventDefault();
                fetchData(link.href);
            }
        });
    }

    function performSearch(query) {
        // Recover active sort and order parameters from current browser URL
        const urlParams = new URL(window.location.href).searchParams;
        const sort = urlParams.get('sort') || 'id';
        const order = urlParams.get('order') || 'asc';
        const department_id = urlParams.get('department_id') || '';
        const designation_id = urlParams.get('designation_id') || '';
        
        let url = `index.php?route=users&search=${encodeURIComponent(query)}&page=1&sort=${sort}&order=${order}`;
        if (department_id) {
            url += `&department_id=${encodeURIComponent(department_id)}`;
        }
        if (designation_id) {
            url += `&designation_id=${encodeURIComponent(designation_id)}`;
        }
        fetchData(url);
    }

    function fetchData(url) {
        // Show loading state subtly in the table if possible
        const tableBody = document.querySelector('tbody');
        if (tableBody) {
            tableBody.style.opacity = '0.5';
            tableBody.style.transition = 'opacity 0.15s ease';
        }

        fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const newCard = doc.querySelector('.card');
                const oldCard = document.querySelector('.card');
                
                if (newCard && oldCard) {
                    oldCard.innerHTML = newCard.innerHTML;
                }
                
                // Update URL history state without reloading the page
                history.replaceState(null, '', url);
            })
            .catch(err => {
                console.error('Error fetching data:', err);
                if (tableBody) {
                    tableBody.style.opacity = '1';
                }
            });
    }
});
</script>

</body>
</html>

