<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets — AssetTracker</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/css/style.css">
    <link rel="stylesheet" href="resources/css/user.css">

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

        /* ── Filter Bar Styles ── */
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
            max-width: 340px;
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
            min-width: 160px;
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
            margin-bottom: 20px;
            padding: 2px 4px;
        }

        /* ── Mobile Responsive Adjustments ── */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: stretch;
                gap: 14px;
            }

            .page-header-actions {
                width: 100%;
            }

            .export-dropdown {
                width: 100%;
            }

            .btn-export {
                width: 100%;
                justify-content: center;
                box-sizing: border-box;
            }

            .dropdown-menu {
                left: 0;
                right: 0;
                width: 100%;
                min-width: 0;
                box-sizing: border-box;
            }

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

            .card {
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .card table {
                min-width: 650px;
            }
        }

        @media (max-width: 480px) {
            .page-header h2 {
                font-size: 20px;
            }

            .page-header p {
                font-size: 12px;
            }

            .card table {
                min-width: 600px;
            }
        }
    </style>
</head>

<body>
    <div class="page">

        <?php view('header'); ?>

        <div class="page-header">

            <div>
                <h2>Assets</h2>
                <p>Manage office assets, availability, and requests from one place.</p>
            </div>

            <div class="page-header-actions">

                <div class="export-dropdown" id="exportDropdown">

                    <button type="button" class="btn-export" onclick="toggleExportMenu(event)">

                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 5v14"></path>
                            <path d="M5 12h14"></path>
                        </svg>

                        Actions

                        <svg class="chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>

                    </button>

                    <div class="dropdown-menu">

                        <?php if ($canManageAssets ?? false): ?>
                            <button type="button" onclick="addAsset()">
                                <span>➕</span> Add Asset
                            </button>
                        <?php endif; ?>

                        <button type="button" onclick="exportPDF()">
                            <span>📄</span> Export PDF
                        </button>

                        <button type="button" onclick="exportExcel()">
                            <span>📊</span> Export Excel
                        </button>

                        <button type="button" onclick="exportCsv()">
                            <span>🖨️</span> Export CSV
                        </button>

                    </div>

                </div>

            </div>
        </div>

        <!-- Success Message -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert-success">
                <svg viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                </svg>
                <div>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Error Message -->
        <?php if (isset($_SESSION['general'])): ?>
            <div class="alert-error">
                <svg viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="8" x2="12" y2="12" />
                    <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <div>
                    <?= htmlspecialchars($_SESSION['general']) ?>
                </div>
            </div>
            <?php unset($_SESSION['general']); ?>
        <?php endif; ?>

        <!-- Filter Form -->
        <form action="index.php" method="get" class="filters-container" id="filterForm">
            <input type="hidden" name="route" value="assets">

            <!-- Search Input -->
            <div class="filter-search-wrap">
                <input type="text" name="search" id="searchInput" placeholder="Search by name, serial no, brand..."
                    value="<?= htmlspecialchars($search ?? '') ?>">
                <svg viewBox="0 0 24 24" id="searchIcon" style="cursor: pointer;">
                    <circle cx="11" cy="11" r="8" />
                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                </svg>
            </div>

            <!-- Category Filter -->
            <select name="category_id" id="categoryFilter" class="filter-select">
                <option value="">All Categories</option>
                <?php foreach ($categories ?? [] as $category): ?>
                    <option value="<?= (int)$category['id'] ?>" <?= (isset($selectedCategoryId) && (int)$selectedCategoryId === (int)$category['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Status Filter -->
            <select name="status" id="statusFilter" class="filter-select">
                <option value="">All Statuses</option>
                <?php foreach ($statuses ?? [] as $statusOption): ?>
                    <option value="<?= htmlspecialchars($statusOption) ?>" <?= (isset($selectedStatus) && strcasecmp($selectedStatus, $statusOption) === 0) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($statusOption) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <!-- Active Filters Feedback Section -->
        <div id="activeFiltersContainer">
            <?php
            $hasActiveCategory = !empty($selectedCategoryId);
            $hasActiveStatus = !empty($selectedStatus);
            $hasActiveSearch = !empty($search);

            $activeCategoryName = null;
            if ($hasActiveCategory && !empty($categories)) {
                foreach ($categories as $cat) {
                    if ((int)$cat['id'] === (int)$selectedCategoryId) {
                        $activeCategoryName = $cat['name'];
                        break;
                    }
                }
            }
            ?>

            <?php if ($hasActiveCategory || $hasActiveStatus || $hasActiveSearch): ?>
                <div class="filter-status">
                    <span style="font-size: 13px; color: var(--slate-500); font-weight: 600; margin-right: 4px;">Active Filters:</span>

                    <?php if ($hasActiveSearch): ?>
                        <span class="badge"
                            style="background: #f8fafc; color: var(--slate-700); padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid var(--slate-300); display: inline-flex; align-items: center; gap: 8px;">
                            Search: "<?= htmlspecialchars($search) ?>"
                            <a href="index.php?route=assets<?= $hasActiveCategory ? '&category_id=' . (int)$selectedCategoryId : '' ?><?= $hasActiveStatus ? '&status=' . urlencode($selectedStatus) : '' ?>"
                                style="color: var(--slate-500); text-decoration: none; font-size: 15px; font-weight: bold; line-height: 1; cursor: pointer;">&times;</a>
                        </span>
                    <?php endif; ?>

                    <?php if ($hasActiveCategory && $activeCategoryName): ?>
                        <span class="badge"
                            style="background: #eff6ff; color: var(--blue); padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid rgba(59, 130, 246, 0.2); display: inline-flex; align-items: center; gap: 8px;">
                            Category: <?= htmlspecialchars($activeCategoryName) ?>
                            <a href="index.php?route=assets<?= $hasActiveSearch ? '&search=' . urlencode($search) : '' ?><?= $hasActiveStatus ? '&status=' . urlencode($selectedStatus) : '' ?>"
                                style="color: var(--blue); text-decoration: none; font-size: 15px; font-weight: bold; line-height: 1; cursor: pointer;">&times;</a>
                        </span>
                    <?php endif; ?>

                    <?php if ($hasActiveStatus): ?>
                        <span class="badge"
                            style="background: #ecfdf5; color: #10b981; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.2); display: inline-flex; align-items: center; gap: 8px;">
                            Status: <?= htmlspecialchars($selectedStatus) ?>
                            <a href="index.php?route=assets<?= $hasActiveSearch ? '&search=' . urlencode($search) : '' ?><?= $hasActiveCategory ? '&category_id=' . (int)$selectedCategoryId : '' ?>"
                                style="color: #10b981; text-decoration: none; font-size: 15px; font-weight: bold; line-height: 1; cursor: pointer;">&times;</a>
                        </span>
                    <?php endif; ?>

                    <a href="index.php?route=assets"
                        style="font-size: 12px; color: var(--slate-400); text-decoration: none; font-weight: 600; margin-left: 8px; transition: color 0.15s ease;"
                        onmouseover="this.style.color='var(--slate-700)'" onmouseout="this.style.color='var(--slate-400)'">
                        Clear Filters
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Assets Table -->
        <div class="card">

            <table>

                <thead>
                    <tr>
                        <th>Asset ID</th>
                        <th>Asset Name</th>
                        <th>Category</th>
                        <th>Serial Number</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (empty($assets ?? [])): ?>

                        <tr>
                            <td colspan="6" class="empty-state" style="text-align: center; padding: 36px 20px;">
                                <?= ($hasActiveCategory || $hasActiveStatus || $hasActiveSearch) ? 'No assets found matching your filter criteria.' : 'No assets found yet.' ?>
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($assets ?? [] as $asset): ?>

                            <tr>

                                <td style="color: var(--slate-400); font-weight: 600;">
                                    #<?= htmlspecialchars($asset['id'] ?? '') ?>
                                </td>

                                <td>
                                    <a class="asset-name-link" href="index.php?route=assets/show&id=<?= (int)($asset['id'] ?? 0) ?>">
                                        <?= htmlspecialchars($asset['name'] ?? '') ?>
                                    </a>
                                </td>

                                <td style="color: var(--slate-600); font-weight: 500;">
                                    <?= htmlspecialchars($asset['category_name'] ?? 'N/A') ?>
                                </td>

                                <td style="color: var(--slate-600); font-weight: 600;">
                                    <?= htmlspecialchars($asset['serial_number'] ?? '') ?>
                                </td>

                                <td>
                                    <?php $status = strtolower((string)($asset['status'] ?? '')); ?>
                                    <span class="pill pill-<?= htmlspecialchars($status === '' ? 'available' : $status) ?>">
                                        <?= htmlspecialchars($asset['status'] !== '' ? $asset['status'] : 'Available') ?>
                                    </span>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

    <script>
        function toggleExportMenu(event) {
            event.stopPropagation();
            document.getElementById('exportDropdown').classList.toggle('active');
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
            const category = document.getElementById('categoryFilter')?.value;
            const status = document.getElementById('statusFilter')?.value;
            const search = document.getElementById('searchInput')?.value?.trim();
            if (category) params.append('category_id', category);
            if (status) params.append('status', status);
            if (search) params.append('search', search);
            const queryString = params.toString();
            return queryString ? '&' + queryString : '';
        }

        function performSearch() {
            const searchVal = document.getElementById('searchInput')?.value?.trim() || '';
            const categoryVal = document.getElementById('categoryFilter')?.value || '';
            const statusVal = document.getElementById('statusFilter')?.value || '';

            const params = new URLSearchParams();
            params.set('route', 'assets');
            if (searchVal) params.set('search', searchVal);
            if (categoryVal) params.set('category_id', categoryVal);
            if (statusVal) params.set('status', statusVal);

            const url = 'index.php?' + params.toString();
            fetchData(url);
        }

        function fetchData(url, updateHistory = true) {
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
                    const activeCategory = urlObj.searchParams.get('category_id') || '';
                    const activeStatus = urlObj.searchParams.get('status') || '';
                    const activeSearch = urlObj.searchParams.get('search') || '';

                    const categorySelect = document.getElementById('categoryFilter');
                    const statusSelect = document.getElementById('statusFilter');
                    const searchInput = document.getElementById('searchInput');

                    if (categorySelect && categorySelect.value !== activeCategory) {
                        categorySelect.value = activeCategory;
                    }
                    if (statusSelect && statusSelect.value !== activeStatus) {
                        statusSelect.value = activeStatus;
                    }
                    if (searchInput && document.activeElement !== searchInput && searchInput.value !== activeSearch) {
                        searchInput.value = activeSearch;
                    }

                    // Update URL in browser
                    if (updateHistory) {
                        history.replaceState(null, '', url);
                    }

                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                })
                .catch(err => {
                    console.error('Error fetching assets:', err);
                })
                .finally(() => {
                    const currentTableBody = document.querySelector('tbody');
                    if (currentTableBody) {
                        currentTableBody.style.opacity = '1';
                    }
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filterForm');
            const searchInput = document.getElementById('searchInput');
            const searchIcon = document.getElementById('searchIcon');
            const categoryFilter = document.getElementById('categoryFilter');
            const statusFilter = document.getElementById('statusFilter');
            let debounceTimer;

            if (filterForm) {
                filterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    clearTimeout(debounceTimer);
                    performSearch();
                });
            }

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        performSearch();
                    }, 350);
                });
            }

            if (searchIcon) {
                searchIcon.addEventListener('click', function() {
                    clearTimeout(debounceTimer);
                    performSearch();
                });
            }

            if (categoryFilter) {
                categoryFilter.addEventListener('change', function() {
                    clearTimeout(debounceTimer);
                    performSearch();
                });
            }

            if (statusFilter) {
                statusFilter.addEventListener('change', function() {
                    clearTimeout(debounceTimer);
                    performSearch();
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

        function addAsset() {
            window.location.href = 'index.php?route=assets/create';
        }

        function exportPDF() {
            const query = getFilterQueryParams();
            downloadFileWithLoader(
                'index.php?route=assets/pdf' + query,
                'Asset_Report.pdf',
                'Generating PDF document...'
            );
        }

        function exportExcel() {
            const query = getFilterQueryParams();
            downloadFileWithLoader(
                'index.php?route=assets/excel' + query,
                'Asset_Report.xlsx',
                'Preparing Excel spreadsheet...'
            );
        }

        function exportCsv() {
            const query = getFilterQueryParams();
            downloadFileWithLoader(
                'index.php?route=assets/csv' + query,
                'Assets_Report.csv',
                'Preparing CSV file...'
            );
        }
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