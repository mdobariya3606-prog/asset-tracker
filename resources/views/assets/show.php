<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($asset['name'] ?? 'Asset') ?> - Asset Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
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
            width: min(100%, 720px);
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

        /* Avatar Container matching your specifications */
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

        /* Avatar Image matching your specifications */
        .avatar-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar svg {
            width: 30px;
            height: 30px;
            stroke: #fff;
            fill: none;
            stroke-width: 2;
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

        .status-badge {
            display: inline-block;
            margin-top: 12px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .status-available {
            background: #ecfdf3;
            color: #047857;
        }

        .status-assigned {
            background: #eff6ff;
            color: #2563eb;
        }

        .status-repair {
            background: #fff7ed;
            color: #c2410c;
        }

        .status-lost {
            background: #fef2f2;
            color: #dc2626;
        }

        .status-scrap {
            background: #f5f3ff;
            color: #7c3aed;
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
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 12px;
            padding: 0 34px 30px;
        }

        .actions a,
        .actions .disabled,
        .document-dropdown summary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 11px 14px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
            box-sizing: border-box;
            width: 100%;
            height: 42px;
            transition: all 0.2s ease;
        }

        .actions a:hover,
        .document-dropdown summary:hover {
            transform: translateY(-1px);
        }

        .edit {
            color: #fff;
            background: #133458;
            border: 1.5px solid #133458;
        }

        .edit:hover {
            background: #1e4976;
            border-color: #1e4976;
        }

        .delete {
            color: #b91c1c;
            background: #fef2f2;
            border: 1.5px solid #fecaca;
        }

        .delete:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }

        .request {
            color: #047857;
            background: #ecfdf3;
            border: 1.5px solid #a7f3d0;
        }

        .request:hover {
            background: #dcfce7;
            border-color: #86efac;
        }

        .back {
            color: #475569;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
        }

        .back:hover {
            background: #f8fafc;
            color: #1e293b;
            border-color: #94a3b8;
        }

        .history-btn {
            color: #0f766e;
            background: #f0fdfa;
            border: 1.5px solid #99f6e4;
        }

        .history-btn:hover {
            background: #ccfbf1;
            border-color: #5eead4;
            color: #115e59;
        }

        /* Disabled state for "Already Requested" */
        .actions .disabled {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
            background: #e2e8f0;
            color: #64748b;
            border: 1.5px solid #cbd5e1;
        }

        .document-dropdown {
            position: relative;
            width: 100%;
        }

        .document-dropdown summary {
            color: #1d4ed8;
            background: #eff6ff;
            border: 1.5px solid #bfdbfe;
            list-style: none;
        }

        .document-dropdown summary::-webkit-details-marker {
            display: none;
        }

        .document-dropdown summary:hover {
            background: #dbeafe;
            border-color: #93c5fd;
        }

        .document-menu {
            position: absolute;
            left: 0;
            right: 0;
            bottom: calc(100% + 6px);
            z-index: 10;
            overflow: hidden;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .12);
        }

        .document-menu a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 11px 14px;
            color: #1e293b;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            height: auto;
            border-radius: 0;
            border: none;
            text-align: left;
        }

        .document-menu a:hover {
            background: #f1f5f9;
            transform: none;
        }

        @media (max-width: 560px) {
            .details {
                grid-template-columns: 1fr;
            }

            .hero,
            .details {
                padding-left: 24px;
                padding-right: 24px;
            }

            .actions {
                padding-left: 24px;
                padding-right: 24px;
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <main class="card">
        <section class="hero">
            <div class="avatar">
                <?php if (!empty($asset['image'])): ?>
                    <img class="avatar-image"
                        src="<?= htmlspecialchars($asset['image']) ?>"
                        alt="<?= htmlspecialchars($asset['name'] ?? 'Asset image') ?>">
                <?php else: ?>
                    <?= strtoupper(substr($asset['name'] ?? 'A', 0, 1)) ?>
                <?php endif; ?>
            </div>

            <h1><?= htmlspecialchars($asset['name'] ?? '') ?></h1>
            <p>Asset ID: <?= htmlspecialchars($asset['id'] ?? '') ?></p>
            <?php $status = strtolower((string)($asset['status'] ?? '')); ?>
            <span class="status-badge status-<?= htmlspecialchars($status === '' ? 'available' : $status) ?>"><?= htmlspecialchars($asset['status'] ?? '') ?></span>
        </section>

        <section class="details">
            <div class="detail">
                <label>Category</label>
                <span><?= htmlspecialchars($asset['category_name'] ?? 'N/A') ?></span>
            </div>

            <div class="detail">
                <label>Brand</label>
                <span><?= htmlspecialchars($asset['brand'] ?? 'N/A') ?></span>
            </div>

            <div class="detail">
                <label>Model</label>
                <span><?= htmlspecialchars($asset['model'] ?? 'N/A') ?></span>
            </div>

            <div class="detail">
                <label>Serial Number</label>
                <span><?= htmlspecialchars($asset['serial_number'] ?? 'N/A') ?></span>
            </div>

            <div class="detail">
                <label>Purchase Date</label>
                <span><?= htmlspecialchars($asset['purchase_date'] ?? 'N/A') ?></span>
            </div>

            <div class="detail">
                <label>Warranty Date</label>
                <span><?= htmlspecialchars($asset['warranty_date'] ?? 'N/A') ?></span>
            </div>

            <div class="detail">
                <label>Vendor</label>
                <span><?= htmlspecialchars($asset['vendor_name'] ?? 'N/A') ?></span>
            </div>

            <div class="detail">
                <label>Cost</label>
                <span><?= htmlspecialchars($asset['cost'] ?? 'N/A') ?></span>
            </div>

            <div class="detail">
                <label>Assignee</label>
                <span>
                    <?php if (!empty($asset['assignee_id'])): ?>
                        <a href="index.php?route=users/profile&id=<?= $asset['assignee_id']; ?>"
                            style="color: #1e293b; font-weight: 500;">
                            #<?= htmlspecialchars($asset['assignee_id']) ?>
                        </a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </span>
            </div>
        </section>

        <nav class="actions">
            <a class="back" href="index.php?route=assets">
                <i data-lucide="arrow-left" style="width: 15px; height: 15px;"></i>
                Back
            </a>

            <a class="history-btn" href="index.php?route=assets/history&id=<?= (int)$asset['id'] ?>">
                <i data-lucide="history" style="width: 15px; height: 15px;"></i>
                History
            </a>

            <?php if ($canManageAssets): ?>
                <a class="edit" href="index.php?route=assets/edit&id=<?= (int)$asset['id'] ?>">
                    <i data-lucide="edit-3" style="width: 15px; height: 15px;"></i>
                    Edit Asset
                </a>
                <a class="delete" href="index.php?route=assets/delete&id=<?= (int)$asset['id'] ?>"
                    onclick="return confirm('Are you sure you want to delete this asset?');">
                    <i data-lucide="trash-2" style="width: 15px; height: 15px;"></i>
                    Delete
                </a>
            <?php elseif ($canRequestAsset && $isAvailable): ?>
                <?php if ($isAlreadyRequested): ?>
                    <span class="disabled">
                        <i data-lucide="clock" style="width: 15px; height: 15px;"></i>
                        Requested
                    </span>
                <?php else: ?>
                    <a class="request" href="index.php?route=assets/request&id=<?= (int)$asset['id'] ?>">
                        <i data-lucide="send" style="width: 15px; height: 15px;"></i>
                        Request Asset
                    </a>
                <?php endif; ?>
            <?php endif; ?>

            <details class="document-dropdown">
                <summary>
                    <i data-lucide="file-text" style="width: 15px; height: 15px;"></i>
                    Documents
                </summary>

                <div class="document-menu">
                    <a href="index.php?route=assets/invoice&id=<?= (int)$asset['id'] ?>">
                        <i data-lucide="file-check" style="width: 14px; height: 14px; color: #64748b;"></i>
                        Invoice
                    </a>

                    <a href="index.php?route=assets/warranty&id=<?= (int)$asset['id'] ?>">
                        <i data-lucide="shield" style="width: 14px; height: 14px; color: #64748b;"></i>
                        Warranty Card
                    </a>
                </div>
            </details>
        </nav>
    </main>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>