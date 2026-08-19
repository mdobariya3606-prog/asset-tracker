<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignment History: <?= htmlspecialchars($asset['name'] ?? 'Asset') ?> — AssetTracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/css/style.css">

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: #f8fafc;
            font-family: 'Inter', sans-serif;
            color: #0f172a;
            padding: 32px 16px;
        }

        .container {
            max-width: 860px;
            margin: 0 auto;
        }

        /* Top navigation / breadcrumbs */
        .top-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            background: #ffffff;
            color: #475569;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .btn-back:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
            transform: translateX(-2px);
        }

        /* Asset Header Hero Card */
        .asset-hero-card {
            background: linear-gradient(135deg, #133458 0%, #1e4976 100%);
            border-radius: 20px;
            padding: 28px 32px;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(19, 52, 88, 0.25);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }

        .asset-hero-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .asset-avatar {
            width: 68px;
            height: 68px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 1.5px solid rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 700;
            color: #ffffff;
            overflow: hidden;
            flex-shrink: 0;
        }

        .asset-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .asset-hero-info h1 {
            margin: 0 0 6px;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.02em;
        }

        .asset-meta-tags {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 13px;
            opacity: 0.9;
        }

        .asset-meta-tag {
            background: rgba(255, 255, 255, 0.15);
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .status-available { background: #dcfce7; color: #15803d; }
        .status-assigned { background: #dbeafe; color: #1d4ed8; }
        .status-repair { background: #ffedd5; color: #c2410c; }
        .status-lost { background: #fee2e2; color: #b91c1c; }
        .status-scrap { background: #f3e8ff; color: #7e22ce; }

        /* Section Title */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .section-header h2 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .history-count-badge {
            background: #e2e8f0;
            color: #475569;
            font-size: 12px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 12px;
        }

        /* Timeline Container */
        .timeline {
            position: relative;
            padding-left: 36px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 16px;
            bottom: 16px;
            left: 14px;
            width: 3px;
            background: #e2e8f0;
            border-radius: 2px;
        }

        /* Timeline Item */
        .timeline-item {
            position: relative;
            margin-bottom: 32px;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        /* Timeline Node Dot */
        .timeline-node {
            position: absolute;
            left: -36px;
            top: 20px;
            width: 31px;
            height: 31px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2;
            box-shadow: 0 0 0 4px #f8fafc;
            transition: all 0.2s ease;
        }

        .timeline-item.active .timeline-node {
            border-color: #2563eb;
            background: #eff6ff;
            box-shadow: 0 0 0 5px rgba(37, 99, 235, 0.15);
        }

        .timeline-item.returned .timeline-node {
            border-color: #10b981;
            background: #ecfdf5;
        }

        .timeline-node svg {
            width: 14px;
            height: 14px;
        }

        .timeline-item.active .timeline-node svg {
            stroke: #2563eb;
        }

        .timeline-item.returned .timeline-node svg {
            stroke: #10b981;
        }

        /* Timeline Card */
        .timeline-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .timeline-item.active .timeline-card {
            border-color: #bfdbfe;
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.08);
            border-left: 4px solid #2563eb;
        }

        .timeline-item.returned .timeline-card {
            border-left: 4px solid #10b981;
        }

        .timeline-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding-bottom: 16px;
            border-bottom: 1px solid #f1f5f9;
            margin-bottom: 16px;
        }

        /* Assignee Info */
        .assignee-box {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .assignee-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #eff6ff;
            color: #2563eb;
            font-weight: 700;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1.5px solid #dbeafe;
            flex-shrink: 0;
        }

        .assignee-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .assignee-details {
            display: flex;
            flex-direction: column;
        }

        .assignee-name {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .assignee-name:hover {
            color: #2563eb;
        }

        .assignee-sub {
            font-size: 12px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 2px;
        }

        .assignee-badge-dept {
            background: #f1f5f9;
            padding: 1px 6px;
            border-radius: 4px;
            font-weight: 600;
            color: #475569;
        }

        /* Status Badge in Card */
        .badge-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-active-assignment {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid rgba(37, 99, 235, 0.2);
        }

        .badge-returned-assignment {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        /* Timeline Milestone Grid */
        .milestones-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 14px;
            margin-bottom: 16px;
            background: #f8fafc;
            padding: 14px 16px;
            border-radius: 12px;
            border: 1px solid #f1f5f9;
        }

        .milestone-col label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.04em;
            margin-bottom: 4px;
        }

        .milestone-col span {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Notes & Remarks */
        .history-notes {
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-size: 13px;
        }

        .note-row {
            display: flex;
            gap: 8px;
            align-items: flex-start;
            color: #334155;
            line-height: 1.45;
        }

        .note-row strong {
            color: #475569;
            min-width: 70px;
            flex-shrink: 0;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .note-row span {
            color: #1e293b;
            background: #f8fafc;
            padding: 6px 12px;
            border-radius: 8px;
            width: 100%;
            border: 1px solid #f1f5f9;
        }

        /* Empty State */
        .empty-history {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            padding: 48px 24px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
        }

        .empty-icon-wrap {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #f1f5f9;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .empty-history h3 {
            margin: 0 0 8px;
            font-size: 18px;
            color: #0f172a;
        }

        .empty-history p {
            margin: 0;
            color: #64748b;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .asset-hero-card {
                padding: 20px;
            }

            .asset-hero-left {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .timeline-card {
                padding: 16px;
            }

            .milestones-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="container">

        <!-- Top Navigation -->
        <div class="top-nav">
            <a href="index.php?route=assets/show&id=<?= (int)$asset['id'] ?>" class="btn-back">
                <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
                Back to Asset Details
            </a>
        </div>

        <!-- Asset Hero Card -->
        <div class="asset-hero-card">
            <div class="asset-hero-left">
                <div class="asset-avatar">
                    <?php if (!empty($asset['image'])): ?>
                        <img src="<?= htmlspecialchars($asset['image']) ?>" alt="<?= htmlspecialchars($asset['name']) ?>">
                    <?php else: ?>
                        <?= strtoupper(substr($asset['name'] ?? 'A', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <div class="asset-hero-info">
                    <h1><?= htmlspecialchars($asset['name'] ?? '') ?></h1>
                    <div class="asset-meta-tags">
                        <span class="asset-meta-tag">ID: #<?= (int)$asset['id'] ?></span>
                        <span class="asset-meta-tag"><?= htmlspecialchars($asset['category_name'] ?? 'General') ?></span>
                        <?php if (!empty($asset['serial_number'])): ?>
                            <span class="asset-meta-tag">S/N: <?= htmlspecialchars($asset['serial_number']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($asset['brand'])): ?>
                            <span class="asset-meta-tag"><?= htmlspecialchars($asset['brand']) ?> <?= htmlspecialchars($asset['model'] ?? '') ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div>
                <?php $currentStatus = strtolower((string)($asset['status'] ?? 'available')); ?>
                <span class="status-pill status-<?= htmlspecialchars($currentStatus === '' ? 'available' : $currentStatus) ?>">
                    <i data-lucide="circle-dot" style="width: 14px; height: 14px;"></i>
                    <?= htmlspecialchars($asset['status'] ?: 'Available') ?>
                </span>
            </div>
        </div>

        <!-- Section Title -->
        <div class="section-header">
            <h2>
                <i data-lucide="history" style="width: 20px; height: 20px; color: #2563eb;"></i>
                Assignment & Return Timeline
                <span class="history-count-badge"><?= count($history) ?> Record<?= count($history) === 1 ? '' : 's' ?></span>
            </h2>
        </div>

        <?php if (empty($history)): ?>
            <!-- Empty State -->
            <div class="empty-history">
                <div class="empty-icon-wrap">
                    <i data-lucide="calendar-x" style="width: 32px; height: 32px;"></i>
                </div>
                <h3>No Assignment History Yet</h3>
                <p>This asset has not been assigned to or returned by any employee yet.</p>
            </div>
        <?php else: ?>
            <!-- Chronological Timeline -->
            <div class="timeline">
                <?php foreach ($history as $record): ?>
                    <?php
                    $isReturned = ($record['status'] === 'RETURNED');
                    $isActive = in_array($record['status'], ['APPROVED', 'ISSUED'], true);
                    $assignedDate = $record['issued_at'] ?? $record['approved_at'] ?? $record['created_at'];
                    ?>

                    <div class="timeline-item <?= $isActive ? 'active' : 'returned' ?>">
                        <!-- Node Icon -->
                        <div class="timeline-node">
                            <?php if ($isActive): ?>
                                <i data-lucide="user-check"></i>
                            <?php else: ?>
                                <i data-lucide="check-circle-2"></i>
                            <?php endif; ?>
                        </div>

                        <!-- Card Content -->
                        <div class="timeline-card">
                            <div class="timeline-card-header">
                                <!-- Assignee Info -->
                                <div class="assignee-box">
                                    <div class="assignee-avatar">
                                        <?php if (!empty($record['user_profile_image'])): ?>
                                            <img src="storage/profile_images/<?= htmlspecialchars($record['user_profile_image']) ?>"
                                                alt="<?= htmlspecialchars($record['user_name']) ?>">
                                        <?php else: ?>
                                            <?= strtoupper(substr($record['user_name'] ?? 'U', 0, 1)) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="assignee-details">
                                        <a href="index.php?route=users/profile&id=<?= (int)$record['user_id'] ?>" class="assignee-name">
                                            <?= htmlspecialchars($record['user_name'] ?? 'Employee #' . $record['user_id']) ?>
                                        </a>
                                        <div class="assignee-sub">
                                            <span><?= htmlspecialchars($record['user_email'] ?? '') ?></span>
                                            <?php if (!empty($record['department_name'])): ?>
                                                &bull; <span class="assignee-badge-dept"><?= htmlspecialchars($record['department_name']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($record['designation_name'])): ?>
                                                &bull; <span><?= htmlspecialchars($record['designation_name']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status Badge -->
                                <div>
                                    <?php if ($isActive): ?>
                                        <span class="badge-status badge-active-assignment">
                                            Currently Assigned
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-status badge-returned-assignment">
                                            Returned
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Milestone Dates -->
                            <div class="milestones-grid">
                                <div class="milestone-col">
                                    <label>Assigned Date</label>
                                    <span>
                                        <i data-lucide="calendar" style="width: 14px; height: 14px; color: #64748b;"></i>
                                        <?= !empty($assignedDate) ? date('M d, Y', strtotime($assignedDate)) : 'N/A' ?>
                                    </span>
                                </div>

                                <div class="milestone-col">
                                    <label>Due Date</label>
                                    <span>
                                        <i data-lucide="clock" style="width: 14px; height: 14px; color: #64748b;"></i>
                                        <?= !empty($record['due_date']) ? date('M d, Y', strtotime($record['due_date'])) : 'N/A' ?>
                                    </span>
                                </div>

                                <div class="milestone-col">
                                    <label>Return Status</label>
                                    <span>
                                        <?php if ($isReturned): ?>
                                            <i data-lucide="check-circle" style="width: 14px; height: 14px; color: #10b981;"></i>
                                            <?= !empty($record['returned_at']) ? date('M d, Y', strtotime($record['returned_at'])) : 'Returned' ?>
                                        <?php else: ?>
                                            <i data-lucide="activity" style="width: 14px; height: 14px; color: #2563eb;"></i>
                                            In Active Use
                                        <?php endif; ?>
                                    </span>
                                </div>

                                <?php if (!empty($record['approved_by_name'])): ?>
                                    <div class="milestone-col">
                                        <label>Approved By</label>
                                        <span>
                                            <i data-lucide="shield-check" style="width: 14px; height: 14px; color: #64748b;"></i>
                                            <?= htmlspecialchars($record['approved_by_name']) ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Reasons & Remarks -->
                            <div class="history-notes">
                                <?php if (!empty($record['reason'])): ?>
                                    <div class="note-row">
                                        <strong>Reason:</strong>
                                        <span><?= htmlspecialchars($record['reason']) ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($record['remark'])): ?>
                                    <div class="note-row">
                                        <strong>Remark:</strong>
                                        <span><?= htmlspecialchars($record['remark']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>
