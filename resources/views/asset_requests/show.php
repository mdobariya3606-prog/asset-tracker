<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($assetRequest['asset_name'] ?? 'Asset') ?> - Asset Details</title>
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

        .hero-icon {
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

        .hero-icon svg {
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

        .edit,
        .cancel {
            color: #fff;
            background: #133458;
        }

        .delete {
            color: #b91c1c;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }

        .request {
            color: #047857;
            background: #ecfdf3;
            border: 1px solid #a7f3d0;
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

            .hero,
            .details {
                padding-left: 24px;
                padding-right: 24px;
            }

            .actions {
                padding-left: 24px;
                padding-right: 24px;
            }
        }

        .badge {
            display: inline-flex;
            margin-top: 10px;
            /*align-items: center;*/
            padding: 4px 10px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-issued {
            background: #f0fdf4;
            color: #0b2716;
            border: 1px solid #bbf7d0;
        }
    </style>
    <link rel="stylesheet" href="../resources/css/style.css">
</head>

<body>

    <main class="card">
        <section class="hero">
            <div class="hero-icon">
                <?= strtoupper(substr($assetRequest['asset_name'], 0, 1)) ?>
            </div>
            <h1><?= htmlspecialchars($assetRequest['asset_name'] ?? '') ?></h1>
            <p>Asset Request ID: <?= htmlspecialchars($assetRequest['id'] ?? '') ?></p>

            <?php $status = strtolower((string)($assetRequest['status'] ?? '')); ?>
            <span class="badge badge-<?= strtolower($assetRequest['status']) ?>">
                <?= htmlspecialchars($assetRequest['status'] ?? '') ?>
            </span>
        </section>

        <section class="details">
            <!--User Id-->
            <div class="detail">
                <label>User Id</label>
                <span><?= htmlspecialchars($assetRequest['user_id'] ?? 'N/A') ?></span>
            </div>

            <!--Asset Id-->
            <div class="detail">
                <label>Asset Id</label>
                <span><?= htmlspecialchars($assetRequest['asset_id'] ?? 'N/A') ?></span>
            </div>

            <!--Request at-->
            <div class="detail">
                <label>Request at</label>
                <span><?= htmlspecialchars($assetRequest['requested_at'] ?? 'N/A') ?></span>
            </div>

            <!--Reason-->
            <div class="detail">
                <label>Reason</label>
                <span style="color: #1b4e88"><?= htmlspecialchars($assetRequest['reason'] ?? 'N/A') ?></span>
            </div>

            <!--Approved By-->
            <div class="detail">
                <label>Approved By</label>
                <span><?= htmlspecialchars($assetRequest['approved_by'] ?? 'N/A') ?></span>
            </div>

            <!--Approved at-->
            <div class="detail">
                <label>Approved at</label>
                <span><?= htmlspecialchars($assetRequest['approved_at'] ?? 'N/A') ?></span>
            </div>

            <!--Rejected By-->
            <div class="detail">
                <label>Rejected By</label>
                <span><?= htmlspecialchars($assetRequest['rejected_by'] ?? 'N/A') ?></span>
            </div>

            <!--Rejected at-->
            <div class="detail">
                <label>Rejected at</label>
                <span><?= htmlspecialchars($assetRequest['rejected_at'] ?? 'N/A') ?></span>
            </div>

            <!--Issued By-->
            <div class="detail">
                <label>Issued By</label>
                <span><?= htmlspecialchars($assetRequest['issued_by'] ?? 'N/A') ?></span>
            </div>

            <!--Issued at-->
            <div class="detail">
                <label>Issued at</label>
                <span><?= htmlspecialchars($assetRequest['issued_at'] ?? 'N/A') ?></span>
            </div>

            <!--Remarks-->
            <div class="detail">
                <label>Remarks</label>
                <span><?= htmlspecialchars($assetRequest['remarks'] ?? 'N/A') ?></span>
            </div>

            <!--Rejection reason-->
            <div class="detail">
                <label>Rejection reason</label>
                <span><?= htmlspecialchars($assetRequest['rejection_reason'] ?? 'N/A') ?></span>
            </div>

            <!--Returned at-->
            <div class="detail">
                <label>Returned at</label>
                <span><?= htmlspecialchars($assetRequest['returned_at'] ?? 'N/A') ?></span>
            </div>

        </section>
        <nav class="actions">
            <a class="back" href="<?php
                                    if (isset($_SESSION['back'])) {
                                        echo $_SESSION['back'];
                                        unset($_SESSION['back']);
                                    } else {
                                        echo "index.php?route=assets/requests";
                                    }
                                    ?>">Back</a>

            <?php
            if ($canManageRequest) { ?>
                <a class="edit" href="index.php?route=assets/requests/manage&id=<?= (int)$assetRequest['id'] ?>">Manage
                    Request</a>
            <?php } else if (
                $canCancelRequest
                && ($assetRequest['status'] === 'PENDING'
                    || $assetRequest['status'] === 'APPROVED')
            ) { ?>
                <a
                    class="cancel"
                    href="index.php?route=assets/requests/cancel&id=<?= (int)$assetRequest['id'] ?>"
                    onclick="return confirm('Are you sure you want to cancel this request?');">
                    Cancel Request
                </a>
            <?php } ?>
        </nav>
    </main>
</body>
<script>
    function confirmCancel() {
        return confirm('Are you sure to cancel this request, after that it cannot be revert.')
    }
</script>

</html>