<?php
function getSortUrl(string $column, string $currentSort, string $currentOrder, string $search, int $page, ?int $deptId = null, ?int $desigId = null, ?string $role = null): string
{
    $newOrder = ($currentSort === $column && $currentOrder === 'asc') ? 'desc' : 'asc';
    $url = "index.php?route=users&search=" . urlencode($search) . "&page={$page}&sort={$column}&order={$newOrder}";
    if ($deptId !== null) {
        $url .= "&department_id={$deptId}";
    }
    if ($desigId !== null) {
        $url .= "&designation_id={$desigId}";
    }
    if ($role !== null && $role !== '') {
        $url .= "&role=" . urlencode($role);
    }
    return $url;
}

function getSortIndicator(string $column, string $currentSort, string $currentOrder): string
{
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
    <link rel="stylesheet" href="resources/css/user.css">
    <link rel="stylesheet" href="resources/css/print.css">

    <style>
        /* Export Loading Overlay */
        .export-loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-family: inherit;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s ease, visibility 0.25s ease;
        }

        .export-loader-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .export-spinner {
            width: 44px;
            height: 44px;
            border: 4px solid rgba(255, 255, 255, 0.25);
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-bottom: 12px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }


        .page-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .export-dropdown {
            position: relative;
            display: inline-block;
        }

        .btn-export {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            box-shadow: 0 2px 10px rgba(19, 52, 88, .3);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: transform .25s ease, box-shadow .25s ease;
            border: none;
        }

        .btn-export:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(19, 52, 88, .4);
        }

        /* Chevron Rotation Animation */
        .btn-export svg.chevron {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .export-dropdown.active .btn-export svg.chevron {
            transform: rotate(180deg);
        }

        /* Animated Dropdown Menu */
        .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background-color: #ffffff;
            min-width: 160px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            z-index: 1000;
            border: 1px solid #e2e8f0;
            padding: 6px;

            /* Hidden / Initial State for Animation */
            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px) scale(0.96);
            transform-origin: top right;
            transition: opacity 0.2s cubic-bezier(0.16, 1, 0.3, 1),
                transform 0.2s cubic-bezier(0.16, 1, 0.3, 1),
                visibility 0.2s;
            will-change: opacity, transform, visibility;
        }

        /* Active State */
        .export-dropdown.active .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        /* Menu Items Animation & Hover */
        .dropdown-menu button {
            font-family: Inter;
            color: #334155;
            padding: 8px 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            background: none;
            border: none;
            font-size: 13px;
            font-weight: 500;
            text-align: left;
            cursor: pointer;
            border-radius: 6px;
            box-sizing: border-box;
            transition: background-color 0.15s ease, color 0.15s ease, transform 0.15s ease;
        }

        .dropdown-menu button:hover {
            background-color: #f1f5f9;
            color: #0f172a;
            transform: translateX(2px);
        }

        /* ── Filters Bar ── */
        .filters-container {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
        }

        .filter-search-wrap {
            position: relative;
            flex: 1;
            min-width: 240px;
            max-width: 320px;
        }

        .filter-search-wrap input {
            width: 100%;
            padding: 9px 16px 9px 38px;
            background: var(--white);
            border: 1.5px solid var(--slate-200);
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 13px;
            color: var(--slate-800);
            outline: none;
            transition: all 0.2s;
            box-sizing: border-box;
        }

        .filter-search-wrap input:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-search-wrap svg {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            stroke: var(--slate-400);
            fill: none;
            stroke-width: 2;
            cursor: pointer;
        }

        .filter-select {
            padding: 9px 34px 9px 12px;
            background-color: var(--white);
            border: 1.5px solid var(--slate-200);
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 13px;
            color: var(--slate-800);
            outline: none;
            cursor: pointer;
            transition: all 0.2s;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M4 6l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            min-width: 150px;
        }

        .filter-select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-status {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 24px;
            padding: 2px 4px;
        }

        @media (max-width: 768px) {
            .filters-container {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-search-wrap {
                max-width: 100%;
                width: 100%;
            }

            .filter-select {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="page">

        <?php view('header'); ?>

        <!-- Success Message Banner -->
        <?php
        if (isset($_SESSION['success'])): ?>
            <div class="alert-success">
                <svg viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
                <div><?= htmlspecialchars($_SESSION['success']) ?></div>
            </div>
        <?php unset($_SESSION['success']);
        endif;
        ?>

        <?php
        if (isset($_SESSION['login_error'])): ?>
            <div class="alert-error" style="background: #fef2f2; border: 1px solid #fecaca; color: #7f1d1d; border-radius: 12px; padding: 14px 18px; font-size: 14px; font-weight: 500; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="#f87171" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <div><?= htmlspecialchars($_SESSION['login_error']) ?></div>
            </div>
        <?php unset($_SESSION['login_error']);
        endif;
        ?>

        <!-- Header Actions -->
        <div class="page-header">
            <div>
                <h2>Users Directory</h2>

                <?php if ($dashboardUserRole === 'EMPLOYEE'): ?>
                    <p>View your profile and team members</p>
                <?php else: ?>
                    <p>Manage, inspect, and register team members inside the company</p>
                <?php endif; ?>
            </div>

            <div class="header-actions">

                <div class="export-dropdown" id="exportDropdown">

                    <button type="button"
                        class="btn-export"
                        onclick="toggleExportMenu(event)">

                        <svg width="18"
                            height="18"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round">

                            <path d="M12 5v14"></path>
                            <path d="M5 12h14"></path>

                        </svg>

                        Actions

                        <svg
                            class="chevron" width="12"
                            height="12"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2">

                            <polyline points="6 9 12 15 18 9"></polyline>

                        </svg>

                    </button>

                    <div class="dropdown-menu">

                        <?php if ($dashboardUserRole !== 'MANAGER' && $dashboardUserRole !== 'EMPLOYEE'): ?>

                            <button type="button" onclick="registerUser()">
                                <span>➕</span>
                                Register User
                            </button>

                            <button type="button" onclick="deletedUsers()">
                                <span>🗑️</span>
                                Deleted users
                            </button>

                        <?php endif; ?>

                        <button type="button" onclick="exportPDF()">
                            <span>📄</span>
                            Export PDF
                        </button>

                        <button type="button" onclick="exportExcel()">
                            <span>📊</span>
                            Export Excel
                        </button>

                    </div>

                </div>

            </div>
        </div>

        <!-- Filters Form -->
        <form action="index.php" method="get" class="filters-container" id="searchForm">
            <input type="hidden" name="route" value="users">

            <!-- Search Input -->
            <div class="filter-search-wrap">
                <input type="text" name="search" id="searchInput" placeholder="Search by name, email, mobile..."
                    value="<?= htmlspecialchars($search ?? '') ?>">
                <svg viewBox="0 0 24 24" id="searchIcon" style="cursor: pointer;">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
            </div>

            <!-- Department Filter -->
            <select name="department_id" id="departmentFilter" class="filter-select">
                <option value="">All Departments</option>
                <?php foreach ($departments ?? [] as $dept): ?>
                    <option value="<?= (int)$dept['id'] ?>" <?= (isset($departmentId) && (int)$departmentId === (int)$dept['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($dept['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Designation Filter -->
            <select name="designation_id" id="designationFilter" class="filter-select">
                <option value="">All Designations</option>
                <?php foreach ($designations ?? [] as $desig): ?>
                    <option value="<?= (int)$desig['id'] ?>" <?= (isset($designationId) && (int)$designationId === (int)$desig['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($desig['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Role Filter (ADMIN ONLY) -->
            <?php if ($dashboardUserRole === 'ADMIN'): ?>
                <select name="role" id="roleFilter" class="filter-select">
                    <option value="">All Roles</option>
                    <?php foreach ($roles ?? [] as $roleOption): ?>
                        <option value="<?= htmlspecialchars($roleOption) ?>" <?= (isset($roleFilter) && strcasecmp($roleFilter, $roleOption) === 0) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($roleOption) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </form>

        <!-- Active Filters Feedback Section -->
        <div id="activeFiltersContainer">
            <?php
            $hasActiveDept = ($departmentId !== null && isset($activeDeptName));
            $hasActiveDesig = ($designationId !== null && isset($activeDesigName));
            $hasActiveRole = ($roleFilter !== null && $roleFilter !== '');
            $hasActiveSearch = ($search !== '');
            ?>

            <?php if ($hasActiveDept || $hasActiveDesig || $hasActiveRole || $hasActiveSearch): ?>
                <div class="filter-status">
                    <span style="font-size: 13px; color: var(--slate-500); font-weight: 600; margin-right: 4px;">Active Filters:</span>

                    <?php if ($hasActiveSearch): ?>
                        <span class="badge"
                            style="background: #f8fafc; color: var(--slate-700); padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid var(--slate-300); display: inline-flex; align-items: center; gap: 8px;">
                            Search: "<?= htmlspecialchars($search) ?>"
                            <a href="index.php?route=users<?= $hasActiveDept ? '&department_id=' . $departmentId : '' ?><?= $hasActiveDesig ? '&designation_id=' . $designationId : '' ?><?= $hasActiveRole ? '&role=' . urlencode($roleFilter) : '' ?>"
                                style="color: var(--slate-500); text-decoration: none; font-size: 15px; font-weight: bold; line-height: 1; cursor: pointer;">&times;</a>
                        </span>
                    <?php endif; ?>

                    <?php if ($hasActiveDept): ?>
                        <span class="badge"
                            style="background: #eff6ff; color: var(--blue); padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid rgba(59, 130, 246, 0.2); display: inline-flex; align-items: center; gap: 8px;">
                            Department: <?= htmlspecialchars($activeDeptName) ?>
                            <a href="index.php?route=users&search=<?= urlencode($search) ?><?= $hasActiveDesig ? '&designation_id=' . $designationId : '' ?><?= $hasActiveRole ? '&role=' . urlencode($roleFilter) : '' ?>"
                                style="color: var(--blue); text-decoration: none; font-size: 15px; font-weight: bold; line-height: 1; cursor: pointer;">&times;</a>
                        </span>
                    <?php endif; ?>

                    <?php if ($hasActiveDesig): ?>
                        <span class="badge"
                            style="background: #ecfdf5; color: #10b981; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.2); display: inline-flex; align-items: center; gap: 8px;">
                            Designation: <?= htmlspecialchars($activeDesigName) ?>
                            <a href="index.php?route=users&search=<?= urlencode($search) ?><?= $hasActiveDept ? '&department_id=' . $departmentId : '' ?><?= $hasActiveRole ? '&role=' . urlencode($roleFilter) : '' ?>"
                                style="color: #10b981; text-decoration: none; font-size: 15px; font-weight: bold; line-height: 1; cursor: pointer;">&times;</a>
                        </span>
                    <?php endif; ?>

                    <?php if ($hasActiveRole): ?>
                        <span class="badge"
                            style="background: #fdf2f8; color: #db2777; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid rgba(219, 39, 119, 0.2); display: inline-flex; align-items: center; gap: 8px;">
                            Role: <?= htmlspecialchars($roleFilter) ?>
                            <a href="index.php?route=users&search=<?= urlencode($search) ?><?= $hasActiveDept ? '&department_id=' . $departmentId : '' ?><?= $hasActiveDesig ? '&designation_id=' . $designationId : '' ?>"
                                style="color: #db2777; text-decoration: none; font-size: 15px; font-weight: bold; line-height: 1; cursor: pointer;">&times;</a>
                        </span>
                    <?php endif; ?>

                    <a href="index.php?route=users"
                        style="font-size: 12px; color: var(--slate-400); text-decoration: none; font-weight: 600; margin-left: 8px; transition: color 0.15s ease;"
                        onmouseover="this.style.color='var(--slate-700)'" onmouseout="this.style.color='var(--slate-400)'">
                        Clear Filters
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Listings Card -->
        <div class="card">
            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                        <circle cx="9" cy="7" r="4" />
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    </svg>
                    <h3><?= isset($message) ? htmlspecialchars($message) : 'No Users Registered Yet' ?></h3>
                    <p><?= ($search !== '' || $hasActiveDept || $hasActiveDesig || $hasActiveRole) ? 'Try clearing the active filters or using different keywords.' : 'Register a single user or add multiple users to populate the list.' ?></p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table id="usersTable">
                        <thead>
                            <tr>
                                <th style="width: 100px;">
                                    <a href="<?= getSortUrl('id', $sort, $order, $search, $page, $departmentId, $designationId, $roleFilter) ?>"
                                        style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                        ID<?= getSortIndicator('id', $sort, $order) ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="<?= getSortUrl('name', $sort, $order, $search, $page, $departmentId, $designationId, $roleFilter) ?>"
                                        style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                        User Name<?= getSortIndicator('name', $sort, $order) ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="<?= getSortUrl('mobile', $sort, $order, $search, $page, $departmentId, $designationId, $roleFilter) ?>"
                                        style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                        Contact/Mobile<?= getSortIndicator('mobile', $sort, $order) ?>
                                    </a>
                                </th>

                                <th>
                                    <a href="<?= getSortUrl('department', $sort, $order, $search, $page, $departmentId, $designationId, $roleFilter) ?>"
                                        style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                        Department<?= getSortIndicator('department', $sort, $order) ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="<?= getSortUrl('designation', $sort, $order, $search, $page, $departmentId, $designationId, $roleFilter) ?>"
                                        style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                        Designation<?= getSortIndicator('designation', $sort, $order) ?>
                                    </a>
                                </th>

                                <?php if ($dashboardUserRole === 'ADMIN'): ?>
                                    <th>
                                        <a href="<?= getSortUrl('role', $sort, $order, $search, $page, $departmentId, $designationId, $roleFilter) ?>"
                                            style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                            Role<?= getSortIndicator('role', $sort, $order) ?>
                                        </a>
                                    </th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr class="user-row-item">
                                    <td class="user-id">#<?= $user['id'] ?></td>
                                    <td>
                                        <a class="profile-link" href="index.php?route=users/profile&id=<?= $user['id'] ?>">
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    <?php if (!empty($user['profile_image'])):
                                                        $serverPath = __DIR__ . "/../../../storage/profile_images/{$user['profile_image']}"; ?>
                                                        <img src="storage/profile_images/<?= htmlspecialchars($user['profile_image']) ?>?v=<?= filemtime($serverPath) ?>"
                                                            alt="<?= htmlspecialchars($user['name']) ?> profile image">
                                                    <?php else: ?>
                                                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <div class="user-name"><?= htmlspecialchars($user['name']) ?></div>
                                                    <div class="user-email"><?= htmlspecialchars($user['email']) ?></div>
                                                </div>
                                            </div>
                                        </a>
                                    </td>
                                    <td style="font-size: 13px; color: var(--slate-700); font-weight: 500;">
                                        <?= htmlspecialchars($user['mobile']) ?>
                                    </td>
                                    <td class="user-department">
                                        <span class="badge badge-dept">
                                            <?= htmlspecialchars($user['department_name'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <td class="user-designation">
                                        <span class="badge badge-desig">
                                            <?= htmlspecialchars($user['designation_name'] ?? 'N/A') ?>
                                        </span>
                                    </td>
                                    <?php if ($dashboardUserRole === 'ADMIN'): ?>
                                        <td class="user-role">
                                            <span class="badge" style="background: #f1f5f9; color: var(--slate-700); font-weight: 600; font-size: 11px;">
                                                <?= htmlspecialchars($user['role'] ?? 'EMPLOYEE') ?>
                                            </span>
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
                            Showing page <strong><?= $page ?></strong> of <strong><?= $totalPages ?></strong>
                            (<strong><?= $totalUsers ?></strong> total users)
                        </div>
                        <div class="pagination-list">
                            <?php if ($page > 1): ?>
                                <a href="index.php?route=users&page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>&order=<?= urlencode($order) ?><?php if ($departmentId !== null) echo '&department_id=' . $departmentId; ?><?php if ($designationId !== null) echo '&designation_id=' . $designationId; ?><?php if ($roleFilter !== null) echo '&role=' . urlencode($roleFilter); ?>"
                                    class="pagination-link">
                                    &laquo; Prev
                                </a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="index.php?route=users&page=<?= $i ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>&order=<?= urlencode($order) ?><?php if ($departmentId !== null) echo '&department_id=' . $departmentId; ?><?php if ($designationId !== null) echo '&designation_id=' . $designationId; ?><?php if ($roleFilter !== null) echo '&role=' . urlencode($roleFilter); ?>"
                                    class="pagination-link <?= $i === $page ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="index.php?route=users&page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>&order=<?= urlencode($order) ?><?php if ($departmentId !== null) echo '&department_id=' . $departmentId; ?><?php if ($designationId !== null) echo '&designation_id=' . $designationId; ?><?php if ($roleFilter !== null) echo '&role=' . urlencode($roleFilter); ?>"
                                    class="pagination-link">
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
        function toggleExportMenu(event) {
            event.stopPropagation();

            document
                .getElementById('exportDropdown')
                .classList
                .toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('exportDropdown');
            if (dropdown && !dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });

        function showExportLoader(message) {
            const loader = document.getElementById('exportLoader');
            const loaderText = document.getElementById('exportLoaderText');
            if (loaderText) loaderText.textContent = message;
            if (loader) loader.classList.add('active');
        }

        function hideExportLoader() {
            const loader = document.getElementById('exportLoader');
            if (loader) loader.classList.remove('active');
        }

        function getFilterQueryParams() {
            const params = new URLSearchParams();
            const department = document.getElementById('departmentFilter')?.value;
            const designation = document.getElementById('designationFilter')?.value;
            const role = document.getElementById('roleFilter')?.value;
            const search = document.getElementById('searchInput')?.value?.trim();
            if (department) params.append('department_id', department);
            if (designation) params.append('designation_id', designation);
            if (role) params.append('role', role);
            if (search) params.append('search', search);
            const queryString = params.toString();
            return queryString ? '&' + queryString : '';
        }

        function downloadFileWithLoader(url, defaultFilename, loaderText) {
            showExportLoader(loaderText);

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    // Extract filename from Header if present
                    const disposition = response.headers.get('Content-Disposition');
                    let filename = defaultFilename;
                    if (disposition && disposition.includes('filename=')) {
                        const matches = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition);
                        if (matches != null && matches[1]) {
                            filename = matches[1].replace(/['"]/g, '');
                        }
                    }

                    return response.blob().then(blob => ({
                        blob,
                        filename
                    }));
                })
                .then(({
                    blob,
                    filename
                }) => {
                    // Create dynamic download link
                    const downloadUrl = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = downloadUrl;
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();

                    // Cleanup
                    window.URL.revokeObjectURL(downloadUrl);
                    a.remove();
                })
                .catch(err => {
                    console.error('Export failed:', err);
                    alert('Failed to generate export file. Please try again.');
                })
                .finally(() => {
                    hideExportLoader();
                });
        }

        function registerUser() {
            window.location.href = 'index.php?route=users/create';
        }

        function deletedUsers() {
            window.location.href = 'index.php?route=users/deleted';
        }

        function exportPDF() {
            const query = getFilterQueryParams();
            downloadFileWithLoader(
                'index.php?route=users/pdf' + query,
                'Users_Report.pdf',
                'Generating PDF document...'
            );
        }

        function exportExcel() {
            const query = getFilterQueryParams();
            downloadFileWithLoader(
                'index.php?route=users/excel' + query,
                'Users_Report.xlsx',
                'Preparing Excel spreadsheet...'
            );
        }

        function printTable() {
            window.print();
        }

        function performSearch(query = null) {
            // Recover active sort and order parameters from current browser URL
            const urlParams = new URL(window.location.href).searchParams;
            const sort = urlParams.get('sort') || 'id';
            const order = urlParams.get('order') || 'asc';
            const searchVal = query !== null ? query : (document.getElementById('searchInput')?.value?.trim() || '');
            const department_id = document.getElementById('departmentFilter')?.value || '';
            const designation_id = document.getElementById('designationFilter')?.value || '';
            const role = document.getElementById('roleFilter')?.value || '';

            let url = `index.php?route=users&search=${encodeURIComponent(searchVal)}&page=1&sort=${sort}&order=${order}`;
            if (department_id) {
                url += `&department_id=${encodeURIComponent(department_id)}`;
            }
            if (designation_id) {
                url += `&designation_id=${encodeURIComponent(designation_id)}`;
            }
            if (role) {
                url += `&role=${encodeURIComponent(role)}`;
            }
            fetchData(url);
        }

        function fetchData(url, updateHistory = true) {
            // Show loading state subtly in the table if possible
            const tableBody = document.querySelector('tbody');
            if (tableBody) {
                tableBody.style.opacity = '0.5';
                tableBody.style.transition = 'opacity 0.15s ease';
            }

            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    const newCard = doc.querySelector('.card');
                    const oldCard = document.querySelector('.card');
                    if (newCard && oldCard) {
                        oldCard.innerHTML = newCard.innerHTML;
                    }

                    const newActiveFilters = doc.querySelector('#activeFiltersContainer');
                    const oldActiveFilters = document.querySelector('#activeFiltersContainer');
                    if (newActiveFilters && oldActiveFilters) {
                        oldActiveFilters.innerHTML = newActiveFilters.innerHTML;
                    }

                    // Sync inputs with URL params if triggered by badge click / back button
                    const urlObj = new URL(url, window.location.href);
                    const activeDept = urlObj.searchParams.get('department_id') || '';
                    const activeDesig = urlObj.searchParams.get('designation_id') || '';
                    const activeRole = urlObj.searchParams.get('role') || '';
                    const activeSearch = urlObj.searchParams.get('search') || '';

                    const deptSelect = document.getElementById('departmentFilter');
                    const desigSelect = document.getElementById('designationFilter');
                    const roleSelect = document.getElementById('roleFilter');
                    const searchInput = document.getElementById('searchInput');

                    if (deptSelect && deptSelect.value !== activeDept) {
                        deptSelect.value = activeDept;
                    }
                    if (desigSelect && desigSelect.value !== activeDesig) {
                        desigSelect.value = activeDesig;
                    }
                    if (roleSelect && roleSelect.value !== activeRole) {
                        roleSelect.value = activeRole;
                    }
                    if (searchInput && document.activeElement !== searchInput && searchInput.value !== activeSearch) {
                        searchInput.value = activeSearch;
                    }

                    // Update URL history state without reloading the page
                    if (updateHistory) {
                        history.replaceState(null, '', url);
                    }

                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                })
                .catch(err => {
                    console.error('Error fetching data:', err);
                })
                .finally(() => {
                    const currentTableBody = document.querySelector('tbody');
                    if (currentTableBody) {
                        currentTableBody.style.opacity = '1';
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.getElementById('searchForm');
            const searchInput = document.getElementById('searchInput');
            const searchIcon = document.getElementById('searchIcon');
            const departmentFilter = document.getElementById('departmentFilter');
            const designationFilter = document.getElementById('designationFilter');
            const roleFilter = document.getElementById('roleFilter');
            let debounceTimer;

            if (searchForm && searchInput) {
                // Prevent default submit to keep input focus and load dynamically
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    clearTimeout(debounceTimer);
                    performSearch(searchInput.value);
                });

                // Trigger dynamic search as the user types (with 350ms debounce)
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    const query = this.value;
                    debounceTimer = setTimeout(() => {
                        performSearch(query);
                    }, 350);
                });
            }

            if (searchIcon) {
                searchIcon.addEventListener('click', function() {
                    clearTimeout(debounceTimer);
                    performSearch(searchInput?.value);
                });
            }

            if (departmentFilter) {
                departmentFilter.addEventListener('change', function() {
                    clearTimeout(debounceTimer);
                    performSearch();
                });
            }

            if (designationFilter) {
                designationFilter.addEventListener('change', function() {
                    clearTimeout(debounceTimer);
                    performSearch();
                });
            }

            if (roleFilter) {
                roleFilter.addEventListener('change', function() {
                    clearTimeout(debounceTimer);
                    performSearch();
                });
            }

            // Intercept clicks on links inside the card (sorting and paging)
            const cardElement = document.querySelector('.card');
            if (cardElement) {
                cardElement.addEventListener('click', function(e) {
                    const link = e.target.closest('a');
                    const route = link?.href ? new URL(link.href).searchParams.get('route') : null;
                    if (link && route === 'users' && !link.getAttribute('onclick') && !link.classList.contains('profile-link')) {
                        e.preventDefault();
                        fetchData(link.href);
                    }
                });
            }

            // Intercept clicks on active filter removal links
            const activeFiltersContainer = document.getElementById('activeFiltersContainer');
            if (activeFiltersContainer) {
                activeFiltersContainer.addEventListener('click', function(e) {
                    const link = e.target.closest('a');
                    if (link && link.href) {
                        e.preventDefault();
                        fetchData(link.href);
                    }
                });
            }

            window.addEventListener('popstate', function() {
                fetchData(window.location.href, false);
            });
        });
    </script>

    <!-- Exporting Overlay -->
    <div id="exportLoader" class="export-loader-overlay">
        <div class="export-spinner"></div>
        <div id="exportLoaderText" style="font-size: 14px; font-weight: 600;">Generating report...</div>
    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>
