<?php
$role = $_SESSION['user_role'];
$canAdd = $role !== 'EMPLOYEE';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notices — AssetTracker</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="resources/css/style.css">
    <link rel="stylesheet" href="resources/css/user.css">

    <style>
        /* ── Notice Confirmation Button ── */

        .confirm-notice-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            padding: 0;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            background: #eff6ff;
            color: #2563eb;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .confirm-notice-btn:hover {
            background: #dbeafe;
            border-color: #2563eb;
        }

        .confirm-notice-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* ── Confirmed Notice ── */

        .confirmed-notice {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            color: #94a3b8;
        }

        /* ── Notice Dropdown ── */

        .notice-dropdown {
            position: relative;
            display: inline-block;
        }

        .btn-notice {
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

        .btn-notice:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(19, 52, 88, .4);
        }

        .btn-notice svg.chevron {
            transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .notice-dropdown.active .btn-notice svg.chevron {
            transform: rotate(180deg);
        }

        /* ── Animated Dropdown Menu ── */

        .notice-dropdown .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background-color: #ffffff;
            min-width: 180px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1),
                0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            z-index: 1000;
            border: 1px solid #e2e8f0;
            padding: 6px;

            opacity: 0;
            visibility: hidden;
            transform: translateY(-8px) scale(0.96);
            transform-origin: top right;
            transition:
                opacity 0.2s cubic-bezier(0.16, 1, 0.3, 1),
                transform 0.2s cubic-bezier(0.16, 1, 0.3, 1),
                visibility 0.2s;
        }

        .notice-dropdown.active .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        /* ── Dropdown Items ── */

        .notice-dropdown .dropdown-menu a {
            color: #334155;
            padding: 8px 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            font-size: 13px;
            font-weight: 500;
            border-radius: 6px;
            box-sizing: border-box;
            transition:
                background-color 0.15s ease,
                color 0.15s ease,
                transform 0.15s ease;
        }

        .notice-dropdown .dropdown-menu a:hover {
            background-color: #f1f5f9;
            color: #0f172a;
            transform: translateX(2px);
        }
    </style>
</head>

<body>

    <div class="page">

        <?php view('header'); ?>

        <!-- Success Message Banner -->
        <?php
        if (isset($_SESSION['success'])):
        ?>

            <div class="alert-success">

                <svg viewBox="0 0 24 24">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                    <polyline points="22 4 12 14.01 9 11.01" />
                </svg>

                <div>
                    <?= htmlspecialchars($_SESSION['success']) ?>
                </div>

            </div>

        <?php
            unset($_SESSION['success']);
        endif;
        ?>


        <!-- Page Header -->

        <div class="page-header">

            <div>

                <h2>Notices</h2>

                <p>
                    Stay informed with the latest organizational announcements and updates.
                </p>

            </div>


            <?php if ($canAdd): ?>

                <div class="notice-dropdown" id="noticeDropdown">

                    <button
                        type="button"
                        class="btn-notice"
                        onclick="toggleNoticeMenu(event)">

                        <svg
                            viewBox="0 0 24 24"
                            style="width:16px; height:16px; fill:none; stroke:currentColor; stroke-width:2.5;">
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>

                        Add Notice

                        <svg
                            class="chevron"
                            width="12"
                            height="12"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>

                    </button>

                    <div class="dropdown-menu">

                        <a href="index.php?route=notices/create-custom">
                            General Notice
                        </a>

                        <a href="index.php?route=notices/create">
                            Custom Notice
                        </a>

                    </div>

                </div>

            <?php endif; ?>

        </div>


        <!-- Notices Table -->

        <div class="card">

            <?php if (empty($notices)): ?>

                <div class="empty-state">

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor">

                        <rect
                            x="3"
                            y="3"
                            width="18"
                            height="18"
                            rx="2"
                            ry="2" />

                        <line
                            x1="9"
                            y1="3"
                            x2="9"
                            y2="21" />

                    </svg>

                    <h3>
                        No notices registered yet.
                    </h3>

                </div>

            <?php else: ?>

                <table>

                    <thead>

                        <tr>

                            <!-- <th style="width: 100px;">
                                ID
                            </th> -->

                            <th>
                                Title
                            </th>

                            <th>
                                Message
                            </th>

                            <th style="width: 80px; text-align: center;">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <?php foreach ($notices as $notice): ?>

                            <tr>

                                <!-- ID -->

                                <!-- <td
                                    style="
                                        color: var(--slate-400);
                                        font-weight: 600;
                                    ">
                                    #<?= htmlspecialchars($notice['nr_id']) ?>
                                </td> -->


                                <!-- Title -->

                                <td
                                    style="
                                        color: var(--blue);
                                        text-decoration: none;
                                        font-weight: 600;
                                    ">
                                    <?= htmlspecialchars($notice['title']) ?>
                                </td>


                                <!-- Message -->

                                <td
                                    style="
                                        color: var(--slate-500);
                                        font-weight: 600;
                                    ">
                                    <?= htmlspecialchars($notice['message']) ?>
                                </td>


                                <!-- Action -->

                                <td style="text-align: center;">

                                    <?php if (!$notice['confirmed_at']): ?>

                                        <button
                                            type="button"
                                            class="confirm-notice-btn"
                                            data-notice-id="<?= (int)$notice['id'] ?>"
                                            title="Confirm notice"
                                            aria-label="Confirm notice">

                                            <svg
                                                viewBox="0 0 24 24"
                                                width="16"
                                                height="16"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round">

                                                <polyline points="20 6 9 17 4 12"></polyline>

                                            </svg>

                                        </button>

                                    <?php else: ?>

                                        <span
                                            class="confirmed-notice"
                                            title="Confirmed">

                                            <svg
                                                viewBox="0 0 24 24"
                                                width="18"
                                                height="18"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                stroke-linecap="round"
                                                stroke-linejoin="round">

                                                <path
                                                    d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />

                                                <polyline
                                                    points="22 4 12 14.01 9 11.01" />

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


    <script>
        /* ── Notice Dropdown ── */

        function toggleNoticeMenu(event) {

            event.stopPropagation();

            const dropdown = document.getElementById('noticeDropdown');

            if (dropdown) {
                dropdown.classList.toggle('active');
            }
        }


        /* Close dropdown when clicking outside */

        document.addEventListener('click', function() {

            const dropdown = document.getElementById('noticeDropdown');

            if (dropdown) {
                dropdown.classList.remove('active');
            }

        });


        /* ── Notice Confirmation ── */

        document.addEventListener('DOMContentLoaded', () => {

            const confirmButtons =
                document.querySelectorAll('.confirm-notice-btn');


            confirmButtons.forEach(button => {

                button.addEventListener('click', async () => {

                    const noticeId =
                        button.dataset.noticeId;


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

                            throw new Error(
                                'Failed to confirm notice.'
                            );

                        }


                        // ==========================================
                        // Change button to confirmed state
                        // ==========================================

                        button.outerHTML = `
                            <span
                                class="confirmed-notice"
                                title="Confirmed">

                                <svg
                                    viewBox="0 0 24 24"
                                    width="18"
                                    height="18"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round">

                                    <path
                                        d="M22 11.08V12a10 10 0 1 1-5.93-9.14"
                                    />

                                    <polyline
                                        points="22 4 12 14.01 9 11.01"
                                    />

                                </svg>

                            </span>
                        `;


                        // ==========================================
                        // Decrease Notices notification badge
                        // ==========================================

                        const badge =
                            document.querySelector(
                                '.notification-badge.notice'
                            );


                        if (badge) {

                            let count = parseInt(
                                badge.textContent.trim(),
                                10
                            );


                            if (count > 1) {

                                badge.textContent =
                                    count - 1;

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

</body>

</html>