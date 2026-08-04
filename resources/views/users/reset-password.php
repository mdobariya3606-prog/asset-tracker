<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; background: #f1f5f9; font-family: Inter, sans-serif; color: #1e293b; }
        .card { width: min(100%, 480px); padding: 36px; background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; box-shadow: 0 8px 32px rgba(15, 23, 42, .08); }
        h1 { margin: 0 0 8px; font-size: 24px; } .subtitle { margin: 0 0 28px; color: #64748b; font-size: 14px; }
        label { display: block; margin: 18px 0 8px; color: #475569; font-size: 13px; font-weight: 600; }
        input { width: 100%; padding: 12px 14px; border: 1.5px solid #cbd5e1; border-radius: 10px; font: inherit; }
        input:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59,130,246,.12); }
        .error { margin-top: 6px; color: #dc2626; font-size: 12px; } .alert { padding: 12px 14px; background: #fef2f2; color: #991b1b; border-radius: 10px; font-size: 13px; }
        .actions { display: flex; gap: 12px; margin-top: 28px; } .actions a, .actions button { flex: 1; padding: 12px; border-radius: 10px; font: 600 14px Inter, sans-serif; text-align: center; text-decoration: none; cursor: pointer; }
        .cancel { color: #475569; border: 1px solid #cbd5e1; background: #fff; } button { border: 0; color: #fff; background: #7c3aed; }
    </style>
</head>
<body>
    <main class="card">
        <h1>Reset User Password</h1>
        <p class="subtitle">Set a new password for <?= htmlspecialchars($user['name']) ?>. The old password is not required.</p>
        <?php if (!empty($errors['general'])): ?><div class="alert"><?= htmlspecialchars($errors['general']) ?></div><?php endif; ?>
        <form method="post" action="index.php?route=users/reset-password&id=<?= (int) $user['id'] ?>">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" minlength="6" required autofocus>
            <?php if (!empty($errors['password'])): ?><div class="error"><?= htmlspecialchars($errors['password']) ?></div><?php endif; ?>
            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" minlength="6" required>
            <?php if (!empty($errors['password_confirmation'])): ?><div class="error"><?= htmlspecialchars($errors['password_confirmation']) ?></div><?php endif; ?>
            <div class="actions">
                <a class="cancel" href="index.php?route=users">Cancel</a>
                <button type="submit">Reset Password</button>
            </div>
        </form>
    </main>
</body>
</html>
