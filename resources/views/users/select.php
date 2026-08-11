<?php
function getSortUrl(string $column, string $currentSort, string $currentOrder, string $search, int $page, ?int $deptId = null, ?int $desigId = null): string
{
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
    <link rel="stylesheet" href="../resources/css/user.css">

    <style>
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
    </style>
</head>

<body>

    <div class="page">

        <?php include '../resources/views/layouts/header.php'; ?>

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

                        <?php if ($dashboardUserRole !== 'EMPLOYEE'): ?>

                            <button type="button" onclick="registerUser()">
                                <span>➕</span>
                                Register User
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

                        <button type="button" onclick="printTable()">
                            <span>🖨️</span>
                            Print
                        </button>

                    </div>

                </div>

            </div>
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
            <input type="text" name="search" id="searchInput" placeholder="Search by name, email, department..."
                value="<?= htmlspecialchars($search ?? '') ?>">
            <svg viewBox="0 0 24 24" style="cursor: pointer;" onclick="document.getElementById('searchForm').submit();">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
        </form>

        <!-- Active Filters Feedback Section -->
        <?php if ($departmentId !== null || $designationId !== null): ?>
            <div class="filter-status"
                style="display: flex; align-items: center; flex-wrap: wrap; gap: 8px; margin-bottom: 24px; padding: 2px 4px;">
                <span style="font-size: 13px; color: var(--slate-500); font-weight: 600; margin-right: 4px;">Active Filters:</span>
                <?php if ($departmentId !== null && isset($activeDeptName)): ?>
                    <span class="badge"
                        style="background: #eff6ff; color: var(--blue); padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid rgba(59, 130, 246, 0.2); display: inline-flex; align-items: center; gap: 8px;">
                        Department: <?= htmlspecialchars($activeDeptName) ?>
                        <a href="index.php?route=users&search=<?= urlencode($search) ?><?php if ($designationId !== null) echo '&designation_id=' . $designationId; ?>"
                            style="color: var(--blue); text-decoration: none; font-size: 15px; font-weight: bold; line-height: 1; cursor: pointer;">&times;</a>
                    </span>
                <?php endif; ?>
                <?php if ($designationId !== null && isset($activeDesigName)): ?>
                    <span class="badge"
                        style="background: #ecfdf5; color: #10b981; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.2); display: inline-flex; align-items: center; gap: 8px;">
                        Designation: <?= htmlspecialchars($activeDesigName) ?>
                        <a href="index.php?route=users&search=<?= urlencode($search) ?><?php if ($departmentId !== null) echo '&department_id=' . $departmentId; ?>"
                            style="color: #10b981; text-decoration: none; font-size: 15px; font-weight: bold; line-height: 1; cursor: pointer;">&times;</a>
                    </span>
                <?php endif; ?>
                <a href="index.php?route=users"
                    style="font-size: 12px; color: var(--slate-400); text-decoration: none; font-weight: 600; margin-left: 8px; transition: color 0.15s ease;"
                    onmouseover="this.style.color='var(--slate-700)'" onmouseout="this.style.color='var(--slate-400)'">
                    Clear Filters
                </a>
            </div>
        <?php endif; ?>

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
                    <p><?= ($search ?? '') !== '' ? 'Try clearing the search filter or using different keywords.' : 'Register a single user or add multiple users to populate the list.' ?></p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table id="usersTable">
                        <thead>
                            <tr>
                                <th style="width: 100px;">
                                    <a href="<?= getSortUrl('id', $sort, $order, $search, $page, $departmentId, $designationId) ?>"
                                        style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                        ID<?= getSortIndicator('id', $sort, $order) ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="<?= getSortUrl('name', $sort, $order, $search, $page, $departmentId, $designationId) ?>"
                                        style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                        User Name<?= getSortIndicator('name', $sort, $order) ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="<?= getSortUrl('mobile', $sort, $order, $search, $page, $departmentId, $designationId) ?>"
                                        style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                        Contact/Mobile<?= getSortIndicator('mobile', $sort, $order) ?>
                                    </a>
                                </th>

                                <th>
                                    <a href="<?= getSortUrl('department', $sort, $order, $search, $page, $departmentId, $designationId) ?>"
                                        style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                        Department<?= getSortIndicator('department', $sort, $order) ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="<?= getSortUrl('designation', $sort, $order, $search, $page, $departmentId, $designationId) ?>"
                                        style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                        Designation<?= getSortIndicator('designation', $sort, $order) ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="<?= getSortUrl('role', $sort, $order, $search, $page, $departmentId, $designationId) ?>"
                                        style="text-decoration: none; color: inherit; display: inline-flex; align-items: center; gap: 4px;">
                                        Role<?= getSortIndicator('role', $sort, $order) ?>
                                    </a>
                                </th>
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
                                                    <?php if (!empty($user['profile_image'])): ?>
                                                        <img src="../storage/profile_images/<?= htmlspecialchars($user['profile_image']) ?>"
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
                                    <td>
                                        <span class="badge badge-role-<?= strtolower($user['role'] ?? 'employee') ?>">
                                            <?= htmlspecialchars($user['role'] ?? 'EMPLOYEE') ?>
                                        </span>
                                    </td>
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
                                <a href="index.php?route=users&page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>&order=<?= urlencode($order) ?><?php if ($departmentId !== null) echo '&department_id=' . $departmentId; ?><?php if ($designationId !== null) echo '&designation_id=' . $designationId; ?>"
                                    class="pagination-link">
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
                                <a href="index.php?route=users&page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&sort=<?= urlencode($sort) ?>&order=<?= urlencode($order) ?><?php if ($departmentId !== null) echo '&department_id=' . $departmentId; ?><?php if ($designationId !== null) echo '&designation_id=' . $designationId; ?>"
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

        window.addEventListener('click', function(event) {
            const dropdown = document.getElementById('exportDropdown');

            if (dropdown && !dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });

        function registerUser() {
            window.location.href = 'index.php?route=users/create';
        }

        function exportPDF() {
            window.location.href = 'index.php?route=users/pdf';
        }

        function exportExcel() {
            window.location.href = 'index.php?route=users/excel';
        }

        function printTable() {
            window.print();
        }

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