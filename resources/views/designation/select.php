<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designations — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --blue:      #3b82f6;
            --blue-dark: #2563eb;
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
            --radius-lg: 12px;
            --radius-md: 8px;
            --radius-sm: 6px;
            --shadow-sm: 0 1px 2px 0 rgba(0,0,0,0.05);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            --red: #ef4444;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            min-height: 100vh;
            color: var(--slate-800);
            padding: 40px 24px;
        }
        .page {
            max-width: 1100px;
            margin: 0 auto;
        }
        .navbar {
            display: flex; justify-content: space-between; align-items: center;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 16px 24px;
            border-radius: var(--radius-lg);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: var(--shadow-md);
            margin-bottom: 32px;
        }
        .logo-section {
            display: flex; align-items: center; gap: 10px;
        }
        .logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            border-radius: var(--radius-md);
            display: flex; align-items: center; justify-content: center;
            color: var(--white);
            box-shadow: 0 4px 12px rgba(59,130,246,.3);
        }
        .logo-icon svg {
            width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 2;
        }
        .logo-text h1 {
            font-size: 16px; font-weight: 700; color: var(--slate-900);
        }
        .logo-text span {
            font-size: 11px; color: var(--slate-500); display: block; margin-top: -2px;
        }
        .nav-links {
            display: flex; align-items: center; gap: 16px;
        }
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 18px;
            border-radius: var(--radius-sm);
            font-family: inherit; font-size: 13px; font-weight: 600;
            text-decoration: none; cursor: pointer;
            transition: all .25s ease;
            border: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: #fff;
            box-shadow: 0 2px 10px rgba(124,58,237,.3);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(124,58,237,.4);
        }
        .btn-logout {
            background: #fef2f2;
            color: var(--red);
            border: 1.5px solid #fecaca;
        }
        .btn-logout:hover {
            background: #fee2e2;
        }
        .btn-secondary {
            background: var(--white);
            color: var(--blue);
            border: 1.5px solid #bfdbfe;
        }
        .btn-secondary:hover {
            background: #eff6ff;
        }
        .page-header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 24px;
        }
        .page-header h2 {
            font-size: 22px; font-weight: 700; color: var(--slate-900);
        }
        .page-header p {
            font-size: 14px; color: var(--slate-500); margin-top: 2px;
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
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }
        th, td {
            padding: 14px 20px;
            border-bottom: 1px solid var(--slate-205);
            font-size: 14px;
        }
        th {
            background: var(--slate-50);
            color: var(--slate-700);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        td {
            color: var(--slate-800);
            font-weight: 500;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .empty-state {
            text-align: center;
            padding: 60px 24px;
        }
        .empty-state svg {
            width: 64px; height: 64px; stroke: var(--slate-400); stroke-width: 1.5; margin-bottom: 16px;
        }
    </style>
</head>
<body>
<div class="page">
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
        <div class="nav-links">
            <div class="user-greeting" style="font-size: 13px; color: var(--slate-600); font-weight: 500; margin-right: 8px;">
                Hello, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></strong>
                <span class="badge badge-role-<?= strtolower($_SESSION['user_role'] ?? 'admin') ?>" style="margin-left: 6px; font-size: 11px; padding: 3px 8px; background: #eff6ff; color: var(--blue); border-radius: 4px; font-weight: 600;">
                    <?= htmlspecialchars($_SESSION['user_role'] ?? 'ADMIN') ?>
                </span>
            </div>
            <a href="index.php?route=logout" class="btn btn-logout" style="padding: 6px 12px; font-size: 13px; min-height: auto;">
                Logout
            </a>
        </div>
    </header>

    <nav class="admin-tabs">
        <a href="index.php?route=users" class="tab-link">Users</a>
        <a href="index.php?route=departments" class="tab-link">Departments</a>
        <a href="index.php?route=designations" class="tab-link active">Designations</a>
    </nav>

    <div class="page-header">
        <div>
            <h2>Designations</h2>
            <p>Configure structural enterprise job levels and designations</p>
        </div>
        <a href="index.php?route=designations/create" class="btn btn-primary">
            <svg viewBox="0 0 24 24" style="width:16px; height:16px; fill:none; stroke:currentColor; stroke-width:2.5;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Designation
        </a>
    </div>

    <?php if (isset($message)): ?>
        <p class="error" style="color: red; margin-bottom: 16px; font-weight: 500;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <div class="card">
        <?php if (empty($designations)): ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><path d="M12 8v8"/><path d="M8 12h8"/></svg>
                <h3>No designations registered yet.</h3>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th style="width: 100px;">ID</th>
                        <th>Designation Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($designations as $desig): ?>
                        <tr>
                            <td style="color: var(--slate-400); font-weight: 600;"><?= htmlspecialchars($desig['id']) ?></td>
                            <td><a href="index.php?route=users&designation_id=<?= $desig['id'] ?>" style="color: var(--blue); text-decoration: none; font-weight: 600;"><?= htmlspecialchars($desig['name']) ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
