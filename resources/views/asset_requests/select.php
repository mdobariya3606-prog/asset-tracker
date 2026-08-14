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
    </style>
</head>

<body>
    <div class="page">
        <?php view('header'); ?>

        <div class="page-header">
            <div>
                <h2>Assets</h2>
                <p>Manage asset requests and approvals efficiently from one place.</p>
            </div>

            <div class="page-header-actions">
                <!-- Export Dropdown -->
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
                        <button type="button" onclick="exportPDF()">📄 PDF</button>
                        <button type="button" onclick="exportExcel()">📊 Excel</button>
                        <button type="button" onclick="printTable()">🖨️ Print</button>
                    </div>
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
        </div>

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

        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <?php
                        $role = $_SESSION['user_role'];
                        $canAccess = $role === 'ADMIN' || $role === 'MANAGER';
                        if ($canAccess) {
                        ?>
                            <th>User Id</th>
                        <?php } ?>
                        <th>Asset Id</th>
                        <th>Status</th>
                        <th>Requested At</th>
                        <th>Due Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($requests ?? [])): ?>
                        <tr>
                            <td colspan="3" class="empty-state">No requests found yet.</td>
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
                                </td>
                                <?php if ($canAccess) { ?>
                                    <td>
                                        <a href="index.php?route=users/profile&id=<?= $request['user_id'] ?>"
                                            style="color: var(--slate-500); font-weight: 600;"
                                            onclick="<?php $_SESSION['back'] = 'index.php?route=assets/requests'; ?>">
                                            #<?= htmlspecialchars($request['user_id'] ?? '') ?>
                                        </a>
                                    </td>
                                <?php } ?>

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

                                <td style="color: var(--slate-500); font-weight: 600;">
                                    <?= htmlspecialchars($request['due_date'] ?? '---') ?>
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

        function exportPDF() {
            downloadFileWithLoader(
                'index.php?route=assets/requests/pdf',
                'asset_requests.pdf',
                'Generating PDF document...'
            );
        }

        function exportExcel() {
            downloadFileWithLoader(
                'index.php?route=assets/requests/excel',
                'asset_requests.xlsx',
                'Preparing Excel spreadsheet...'
            );
        }
    </script>

    <!-- Exporting Overlay -->
    <div id="exportLoader" class="export-loader-overlay">
        <div class="export-spinner"></div>
        <div id="exportLoaderText" style="font-size: 14px; font-weight: 600;">Generating report...</div>
    </div>
</body>

</html>