<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($user['name']) ?> - Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background: #f1f5f9;
            font-family: Inter, sans-serif;
            color: #1e293b;
        }

        .card {
            width: min(100%, 620px);
            overflow: hidden;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(15, 23, 42, .08);
        }

        .hero {
            padding: 34px;
            color: #fff;
            background: #133458;
        }

        .avatar {
            width: 64px;
            height: 64px;
            display: grid;
            place-items: center;
            margin-bottom: 18px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .2);
            font-size: 26px;
            font-weight: 700;
            overflow: hidden;
        }

        .avatar-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        h1 {
            margin: 0 0 6px;
            font-size: 26px;
        }

        .hero p {
            margin: 0;
            opacity: .85;
            font-size: 14px;
        }

        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            padding: 30px 34px;
        }

        .detail label {
            display: block;
            margin-bottom: 6px;
            color: #64748b;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .4px;
        }

        .detail span {
            color: #1e293b;
            font-size: 15px;
            font-weight: 500;
            overflow-wrap: anywhere;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 0 34px 30px;
        }

        .actions a {
            flex: 1 1 140px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
        }

        .edit {
            color: #fff;
            background: #133458;
        }

        .reset {
            color: #92400e;
            background: #fef3c7;
            border: 1px solid #fde68a;
        }

        .delete {
            color: #b91c1c;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .back {
            color: #475569;
            background: #fff;
            border: 1px solid #cbd5e1;
        }

        @media (max-width: 560px) {
            .details {
                grid-template-columns: 1fr;
            }

            .hero, .details {
                padding-left: 24px;
                padding-right: 24px;
            }

            .actions {
                padding-left: 24px;
                padding-right: 24px;
            }
        }
    </style>
</head>
<body>
<main class="card">
    <section class="hero">
        <?php if (!empty($user['profile_image'])): ?>
            <div class="avatar"><img class="avatar-image"
                                     src="../storage/profile_images/<?= htmlspecialchars($user['profile_image']) ?>"
                                     alt="Profile image"></div>
        <?php else: ?>
            <div class="avatar">
                <?php if (!empty($user['profile_image'])): ?>
                    <img class="avatar-image"
                         src="../storage/profile_images/<?= htmlspecialchars($user['profile_image']) ?>"
                         alt="Profile image">
                <?php else: ?>
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <h1><?= htmlspecialchars($user['name']) ?></h1>
        <p><?= htmlspecialchars($user['email']) ?></p>
    </section>
    <section class="details">
        <div class="detail"><label>Mobile</label><span><?= htmlspecialchars($user['mobile'] ?? 'N/A') ?></span></div>
        <div class="detail"><label>Role</label><span><?= htmlspecialchars($user['role'] ?? 'EMPLOYEE') ?></span></div>
        <div class="detail">
            <label>Department</label><span><?= htmlspecialchars($user['department_name'] ?? 'N/A') ?></span></div>
        <div class="detail">
            <label>Designation</label><span><?= htmlspecialchars($user['designation_name'] ?? 'N/A') ?></span></div>
    </section>
    <nav class="actions">
        <a class="back" href="<?= $_SESSION['back'] ?? 'index.php?route=users' ?>">Back</a>
        <?php if ($canEditProfile): ?>
            <a class="edit" href="index.php?route=users/edit&id=<?= (int)$user['id'] ?>">Edit Profile</a>
        <?php endif; ?>
        <?php if ($canManageResetOrDelete): ?>
            <a class="reset" href="index.php?route=users/reset-password&id=<?= (int)$user['id'] ?>">Reset Password</a>
            <a class="delete" href="index.php?route=users/delete&id=<?= (int)$user['id'] ?>"
               onclick="return confirm('Are you sure you want to delete this user?');">Delete Profile</a>
        <?php endif; ?>
    </nav>
</main>
</body>
</html>