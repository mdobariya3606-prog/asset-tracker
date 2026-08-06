<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designations — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/style.css">
</head>
<body>
<div class="page">
    <?php include '../resources/views/layouts/header.php'; ?>

    <div class="page-header">
        <div>
            <h2>Designations</h2>
            <p>Configure structural enterprise job levels and designations</p>
        </div>
        <?php if (($role ?? 'EMPLOYEE') === 'ADMIN'): ?>
            <a href="index.php?route=designations/create" class="btn btn-primary">
                <svg viewBox="0 0 24 24"
                     style="width:16px; height:16px; fill:none; stroke:currentColor; stroke-width:2.5;">
                    <line x1="12" y1="5" x2="12" y2="19"/>
                    <line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Add Designation
            </a>
        <?php endif; ?>
    </div>

    <div class="card">
        <?php if (empty($designations)): ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 8v8"/>
                    <path d="M8 12h8"/>
                </svg>
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
                        <td style="color: var(--slate-400); font-weight: 600;">
                            #<?= htmlspecialchars($desig['id']) ?></td>
                        <td><a href="index.php?route=users&designation_id=<?= $desig['id'] ?>"
                               style="color: var(--blue); text-decoration: none; font-weight: 600;"><?= htmlspecialchars($desig['name']) ?></a>
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