<style>
    /* ── Navigation / Header ── */
    .navbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--white, #ffffff);
        border: 1px solid var(--slate-200, #e2e8f0);
        border-radius: var(--radius-md, 8px);
        padding: 16px 24px;
        box-shadow: var(--shadow-md, 0 4px 6px -1px rgba(0, 0, 0, 0.1));
        margin-bottom: 32px;
        gap: 16px;
    }

    .logo-section {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }

    .logo-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--blue, #3b82f6), var(--cyan, #06b6d4));
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(59, 130, 246, .25);
        flex-shrink: 0;
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
        color: var(--slate-900, #0f172a);
        letter-spacing: -.3px;
        margin: 0;
        line-height: 1.2;
    }

    .logo-text span {
        font-size: 11px;
        color: var(--slate-400, #94a3b8);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .nav-user {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-shrink: 0;
    }

    .avatar-badge {
        width: 36px;
        height: 36px;
        background: var(--slate-100, #f1f5f9);
        border: 1.5px solid var(--slate-200, #e2e8f0);
        color: var(--slate-700, #334155);
        font-weight: 600;
        font-size: 13px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }

    /* Standard Button Styling */
    .nav-user .btn-icon-text {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 7px 12px;
        font-size: 12px;
        border-radius: 6px;
        text-decoration: none;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all 0.2s ease;
    }

    .nav-user form {
        margin: 0;
        display: inline-block;
    }

    /* Tab Layout & Badges */
    .admin-tabs {
        display: flex;
        /* gap: 8px; */
        border-bottom: 1px solid var(--slate-200, #e2e8f0);
        margin-bottom: 24px;
    }

    .tab-label {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .notification-badge {
        position: absolute;
        top: -8px;
        right: -18px;
        min-width: 18px;
        height: 18px;
        padding: 0 5px;
        border-radius: 999px;
        background: #25D366;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        line-height: 18px;
        text-align: center;
        box-sizing: border-box;
    }

    .notification-badge.notice {
        background: #ef4444;
    }

    /* ── Mobile Responsive Rules ── */
    @media (max-width: 768px) {

        .navbar {
            padding: 10px 12px;
            margin-bottom: 16px;
            gap: 8px;
            min-width: 0;
        }

        .logo-section {
            gap: 8px;
            min-width: 0;
        }

        .logo-icon {
            width: 25px;
            height: 25px;
            border-radius: 9px;
        }

        .logo-icon svg {
            width: 14px;
            height: 12px;
        }

        .logo-text {
            min-width: 0;
        }

        .logo-text h1 {
            font-size: 16px;
            white-space: nowrap;
        }

        .logo-text span {
            display: none;
        }

        .nav-user {
            gap: 6px;
            min-width: 0;
        }

        /* Convert buttons to circular icon-only buttons */
        .nav-user .btn-icon-text {
            width: 34px;
            height: 34px;
            padding: 0 !important;
            border-radius: 50% !important;
            box-sizing: border-box;
            flex-shrink: 0;
        }

        /* Hide text spans inside action buttons */
        .nav-user .btn-icon-text .btn-text {
            display: none;
        }

        .nav-user .btn-icon-text svg {
            width: 16px !important;
            height: 16px !important;
            margin: 0;
        }

        .avatar-badge {
            width: 34px;
            height: 34px;
        }

        /* Responsive Admin Navigation Tabs */
        .admin-tabs {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            overflow-y: visible;
            white-space: nowrap;
            padding: 8px 4px 10px;
            margin-bottom: 16px;
            scrollbar-width: none;
            -ms-overflow-style: none;
            -webkit-overflow-scrolling: touch;
        }

        .admin-tabs::-webkit-scrollbar {
            display: none;
        }

        .tab-link {
            flex: 0 0 auto;
            white-space: nowrap;
        }

        .tab-label {
            white-space: nowrap;
        }
    }

    /* ── Extra Small Mobile Screens ── */
    @media (max-width: 480px) {

        .navbar {
            padding: 8px 10px;
            gap: 6px;
        }

        .logo-section {
            gap: 6px;
        }

        .logo-icon {
            width: 20px;
            height: 20px;
        }

        .logo-text h1 {
            font-size: 15px;
        }

        .nav-user {
            gap: 5px;
        }

        .avatar-badge {
            width: 32px;
            height: 32px;
        }

        .nav-user .btn-icon-text {
            width: 32px;
            height: 32px;
        }

        .nav-user .btn-icon-text svg {
            width: 15px !important;
            height: 15px !important;
        }
    }
</style>

<?php

use App\Models\User;

$conn = (new App\Config\Database())->getConnection();
$user = (new User($conn))->find($_SESSION['user_id'])[0];

?>

<!-- Navbar -->
<header class="navbar">

    <div class="logo-section">

        <div class="logo-icon">
            <svg viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
            </svg>
        </div>

        <div class="logo-text">
            <a href="index.php?route=users" style="text-decoration: none;">
                <h1>AssetTracker</h1>
            </a>
            <span>System Administration</span>
        </div>

    </div>

    <div class="nav-user">

        <?php

        use App\Config\Database;
        use App\Models\AssetRequest;
        use App\Models\Notice;

        if (!empty($user)): ?>

            <a
                href="index.php?route=users/edit&id=<?= (int)$_SESSION['user_id'] ?>"
                title="Profile">

                <div class="avatar-badge">

                    <?php if (!empty($user['profile_image'])): ?>

                        <?php
                        $serverPath = __DIR__ . "/../../../storage/profile_images/{$user['profile_image']}";
                        $cacheVersion = file_exists($serverPath) ? filemtime($serverPath) : time();
                        ?>

                        <img
                            src="storage/profile_images/<?= htmlspecialchars($user['profile_image']) ?>?v=<?= $cacheVersion ?>"
                            alt="<?= htmlspecialchars($user['name']) ?> profile image"
                            style="width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;">

                    <?php else: ?>

                        <?= strtoupper(substr($user['name'], 0, 1)) ?>

                    <?php endif; ?>

                </div>

            </a>

            <div style="text-align: left; line-height: 1.2;">

                <div style="font-weight: 600; font-size: 13px; color: var(--slate-800);">
                    <?= htmlspecialchars($user['name']) ?>
                </div>

                <div style="font-size: 11px; color: var(--slate-500);">
                    <?= htmlspecialchars($user['email']) ?>
                </div>

            </div>

            <form
                action="index.php?route=logout"
                method="POST"
                style="margin: 0;">

                <button
                    type="submit"
                    title="Sign out"
                    class="btn btn-logout"
                    style="padding: 7px 12px; font-size: 12px;"
                    onclick="return confirm('Are you sure to logout?')">

                    <svg
                        viewBox="0 0 24 24"
                        style="width:14px;height:14px;">

                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />

                        <polyline points="16 17 21 12 16 7" />

                        <line x1="21" y1="12" x2="9" y2="12" />

                    </svg>

                </button>

            </form>

        <?php endif; ?>

    </div>

</header>

<!-- Navigation Tabs -->
<?php $route = $_GET['route']; ?>

<nav class="admin-tabs">

    <a
        href="index.php?route=users"
        class="tab-link <?= $route === 'users' ? 'active' : '' ?>">
        Users
    </a>

    <a
        href="index.php?route=assets"
        class="tab-link <?= $route === 'assets' ? 'active' : '' ?>">
        Assets
    </a>

    <?php
    $pendingRequests = (new AssetRequest($conn))->pendingRequests();
    ?>

    <a
        href="index.php?route=assets/requests"
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

    <?php
    $pendingNotices = (new Notice($conn))->pendingNotices();
    ?>

    <a
        href="index.php?route=notices"
        class="tab-link <?= $route === 'notices' ? 'active' : '' ?>">

        <span class="tab-label">
            Notices

            <?php if ($pendingNotices > 0): ?>

                <span class="notification-badge notice">
                    <?= $pendingNotices > 99 ? '99+' : $pendingNotices ?>
                </span>

            <?php endif; ?>

        </span>

    </a>

    <a
        href="index.php?route=departments"
        class="tab-link <?= $route === 'departments' ? 'active' : '' ?>">
        Departments
    </a>

    <a
        href="index.php?route=designations"
        class="tab-link <?= $route === 'designations' ? 'active' : '' ?>">
        Designations
    </a>

</nav>