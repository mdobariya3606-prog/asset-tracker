<style>
    /* ── Navigation / Header ── */
    .navbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-md);
        padding: 16px 24px;
        box-shadow: var(--shadow-md);
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

    .nav-user {
        display: flex;
        align-items: center;
        gap: 14px;
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

    .tab-label {
        position: relative;
        display: inline-block;
    }

    .notification-badge {
        position: absolute;
        top: -8px;
        right: -22px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 999px;
        background: #25D366;
        /* WhatsApp green */
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        line-height: 18px;
        text-align: center;
        box-sizing: border-box;
    }
</style>
<?php

 $conn = (new App\Config\Database())->getConnection(); ?>
<!-- Navbar -->
<header class="navbar">
    <div class="logo-section">
        <div class="logo-icon">
            <svg viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
            </svg>
        </div>
        <div class="logo-text">
            <h1>AssetTracker</h1>
            <span>System Administration</span>
        </div>
    </div>

    <div class="nav-user">
        <?php

        use App\Config\Database;
        use App\Models\AssetRequest;

        if (!empty($_SESSION)): ?>
            <div class="avatar-badge">
                <?php if (!empty($_SESSION['profile_image'])): ?>
                    <img src="../storage/profile_images/<?= htmlspecialchars($_SESSION['profile_image']) ?>"
                        alt="<?= htmlspecialchars($_SESSION['user_name']) ?> profile image"
                        style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block;">
                <?php else: ?>
                    <?= strtoupper(substr($_SESSION['user_name'], 0, 1)) ?>
                <?php endif; ?>
            </div>
            <div style="text-align: left; line-height: 1.2;">
                <div style="font-weight: 600; font-size: 13px; color: var(--slate-800);"><?= htmlspecialchars($_SESSION['user_name']) ?></div>
                <div style="font-size: 11px; color: var(--slate-500);"><?= htmlspecialchars($_SESSION['user_email']) ?></div>
            </div>
            <a href="index.php?route=users/edit&id=<?= (int)$_SESSION['user_id'] ?>" class="btn btn-secondary"
                style="padding: 6px 12px; font-size: 12px;">
                <svg viewBox="0 0 24 24" style="width:14px;height:14px;">
                    <circle cx="12" cy="8" r="4" />
                    <path d="M4 21a8 8 0 0 1 16 0" />
                </svg>
                Profile
            </a>
            <form action="index.php?route=logout" method="POST" style="margin: 0;">
                <button type="submit" class="btn btn-logout" style="padding: 7px 12px; font-size: 12px;" onclick="return confirm('Are you sure to logout?')">
                    <svg viewBox="0 0 24 24" style="width:14px;height:14px;">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <polyline points="16 17 21 12 16 7" />
                        <line x1="21" y1="12" x2="9" y2="12" />
                    </svg>
                    Sign out
                </button>
            </form>
        <?php endif; ?>
    </div>
</header>

<!-- Admin Navigation Tabs -->
<?php $route = $_GET['route']; ?>
<nav class="admin-tabs">
    <a href="index.php?route=users" class="tab-link <?= $route === 'users' ? 'active' : '' ?>">Users</a>
    <a href="index.php?route=departments"
        class="tab-link <?= $route === 'departments' ? 'active' : '' ?>">Departments</a>
    <a href="index.php?route=designations" class="tab-link <?= $route === 'designations' ? 'active' : '' ?>">Designations</a>
    <a href="index.php?route=assets" class="tab-link <?= $route === 'assets' ? 'active' : '' ?>">Assets</a>

    <?php
    $pendingRequests = (new AssetRequest($conn))->pendingRequests();
    ?>
    <a href="index.php?route=assets/requests"
        class="tab-link <?= $route === 'assets/requests' ? 'active' : '' ?>">
        <span class="tab-label">
            Requests
            <?php if ($pendingRequests > 0): ?>
                <span class="notification-badge">
                    <?= $pendingRequests > 99 ? '99+' : $pendingRequests ?>
                </span>
            <?php endif; ?>
        </span>
    </a>
</nav>