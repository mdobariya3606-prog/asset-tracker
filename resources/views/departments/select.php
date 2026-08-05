<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments — AssetTracker</title>
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
            max-width: 1200px;
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

        .nav-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar-badge {
            width: 36px;
            height: 36px;
            background: var(--slate-100);
            border: 1.5px solid var(--slate-200);
            color: var(--slate-700);
            font-weight: 600;
            font-size: 13px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-user-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .nav-user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--slate-800);
        }

        .nav-user-role {
            font-size: 11px;
            color: var(--slate-500);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 500;
        }

        .btn-secondary {
            background: var(--slate-100);
            color: var(--slate-700);
        }

        .btn-secondary:hover {
            background: var(--slate-200);
        }

        .btn {
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
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th, td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--slate-200);
            font-size: 14px;
            vertical-align: middle;
        }

        th {
            background: var(--slate-50);
            color: var(--slate-600);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: .5px;
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
            width: 64px;
            height: 64px;
            stroke: var(--slate-400);
            stroke-width: 1.5;
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--slate-800);
            margin-bottom: 6px;
        }

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }

            .nav-links {
                width: 100%;
                justify-content: center;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            th, td {
                padding: 12px 14px;
            }
        }
    </style>
</head>
<body>
<div class="page">
    
    <?php include '../resources/views/layouts/header.php'; ?>

    <nav class="admin-tabs">
        <a href="index.php?route=users" class="tab-link">Users</a>
        <a href="index.php?route=departments" class="tab-link active">Departments</a>
        <a href="index.php?route=designations" class="tab-link">Designations</a>
        <a href="index.php?route=assets" class="tab-link">Assets</a>
    </nav>

    <div class="page-header">
        <div>
            <h2>Departments</h2>
            <p>Configure organizational team hierarchy and departments</p>
        </div>
        <?php if (($role ?? 'EMPLOYEE') === 'ADMIN'): ?>
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
                        <td style="color: var(--slate-400); font-weight: 600;"><?= htmlspecialchars($dept['id']) ?></td>
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