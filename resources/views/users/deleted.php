<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/css/style.css">
    <link rel="stylesheet" href="resources/css/user.css">
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

        <div class="page-header">
            <div>
                <h2>Deleted Users</h2>
            </div>
        </div>

        <div class="card">
            <?php if (empty($users)): ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                        <line x1="9" y1="3" x2="9" y2="21" />
                    </svg>
                    <h3>No users are deleted yet.</h3>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 100px;">ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Designation</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td style="color: var(--slate-400); font-weight: 600;">
                                    #<?= htmlspecialchars($user['id']) ?></td>
                                <td><a href="index.php?route=users/profile&id=<?= $user['id'] ?>"
                                        style="color: var(--blue); text-decoration: none; font-weight: 600;"><?= htmlspecialchars($user['name']) ?></a>
                                </td>
                                <td style="color: var(--slate-600); font-weight: 600;">
                                    <?= htmlspecialchars($user['email']) ?></td>
                                <td style="color: var(--slate-600); font-weight: 600;">
                                    <?= htmlspecialchars($user['department']) ?></td>
                                <td style="color: var(--slate-600); font-weight: 600;">
                                    <?= htmlspecialchars($user['designation']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>