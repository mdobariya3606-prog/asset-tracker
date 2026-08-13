<?php $role = $_SESSION['user_role'];
$canAdd = $role !== 'EMPLOYEE';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notices — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/style.css">
    <link rel="stylesheet" href="../resources/css/user.css">
</head>

<body>
    <div class="page">

        <?php include '../resources/views/layouts/header.php'; ?>

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
                <h2>Notices</h2>
                <p>Configure organizational team hierarchy and notices</p>
            </div>
            <?php if ($canAdd): ?>
                <a href="index.php?route=notices/create" class="btn btn-primary">
                    <svg viewBox="0 0 24 24"
                        style="width:16px; height:16px; fill:none; stroke:currentColor; stroke-width:2.5;">
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    Add Notice
                </a>
            <?php endif; ?>
        </div>

        <div class="card">
            <?php if (empty($notices)): ?>
                <div class="empty-state">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                        <line x1="9" y1="3" x2="9" y2="21" />
                    </svg>
                    <h3>No notices registered yet.</h3>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 100px;">ID</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th style="width: 80px; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($notices as $notice): ?>
                            <tr>
                                <!-- id -->
                                <td style="color: var(--slate-400); font-weight: 600;">
                                    #<?= htmlspecialchars($notice['id']) ?></td>

                                <!-- title -->
                                <td><a href="index.php?route=users&department_id=<?= $notice['id'] ?>"
                                        style="color: var(--blue); text-decoration: none; font-weight: 600;"><?= htmlspecialchars($notice['title']) ?></a>
                                </td>

                                <!-- message -->
                                <td style="color: var(--slate-500); font-weight: 600;">
                                    <?= htmlspecialchars($notice['message']) ?></td>

                                <!-- action -->
                                <td style="text-align: center;">
                                    <?php if (!$notice['confirmed_at']): ?>

                                        <button
                                            type="button"
                                            class="confirm-notice-btn"
                                            data-notice-id="<?= (int)$notice['id'] ?>"
                                            title="Mark as confirmed"
                                            style="
                                                display: inline-flex;
                                                align-items: center;
                                                justify-content: center;
                                                width: 32px;
                                                height: 32px;
                                                border-radius: 6px;
                                                border: 1px solid #e2e8f0;
                                                color: #10b981;
                                                background: transparent;
                                                cursor: pointer;
                                                transition: background 0.2s;
                                            "
                                            onmouseover="this.style.background='#ecfdf5'"
                                            onmouseout="this.style.background='transparent'">
                                            <svg
                                                viewBox="0 0 24 24"
                                                width="18"
                                                height="18"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2.5"
                                                stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <polyline points="20 6 9 17 4 12"></polyline>
                                            </svg>
                                        </button>

                                    <?php else: ?>

                                        <span
                                            title="Confirmed"
                                            style="
                                                display: inline-flex;
                                                align-items: center;
                                                justify-content: center;
                                                width: 32px;
                                                height: 32px;
                                                color: #94a3b8;
                                            ">
                                            <svg
                                                viewBox="0 0 24 24"
                                                width="18"
                                                height="18"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                                <polyline points="22 4 12 14.01 9 11.01" />
                                            </svg>
                                        </span>

                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        const confirmButtons = document.querySelectorAll('.confirm-notice-btn');

        confirmButtons.forEach(button => {

            button.addEventListener('click', async () => {

                const noticeId = button.dataset.noticeId;

                if (!noticeId) {
                    return;
                }

                // Prevent multiple clicks
                button.disabled = true;

                try {

                    const response = await fetch(
                        `index.php?route=notices/mark-confirmed&id=${encodeURIComponent(noticeId)}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error('Failed to confirm notice.');
                    }


                    // ==========================================
                    // Change button to confirmed state
                    // ==========================================

                    button.outerHTML = `
                    <span
                        title="Confirmed"
                        style="
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            width: 32px;
                            height: 32px;
                            color: #94a3b8;
                        "
                    >
                        <svg
                            viewBox="0 0 24 24"
                            width="18"
                            height="18"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                    </span>
                `;


                    // ==========================================
                    // Decrease Notices notification badge
                    // ==========================================

                    const badge = document.querySelector(
                        '.notification-badge.notice'
                    );

                    if (badge) {

                        let count = parseInt(
                            badge.textContent.trim(),
                            10
                        );

                        if (count > 1) {

                            badge.textContent = count - 1;

                        } else {

                            badge.remove();

                        }
                    }

                } catch (error) {

                    console.error(
                        'Notice confirmation failed:',
                        error
                    );

                    // Allow the user to try again
                    button.disabled = false;

                    alert(
                        'Unable to confirm the notice. Please try again.'
                    );
                }

            });

        });

    });
</script>


</html>