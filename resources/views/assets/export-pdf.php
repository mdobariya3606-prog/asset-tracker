<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Dompdf\Dompdf;

// Fetch $assets here if they are not already available.
// $assets = ...

$css = file_get_contents('resources/css/pdf.css');

$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assets</title>

    <style>
        ' . $css . '
    </style>
</head>

<body>

    <h1>Assets</h1>

    <p class="subtitle">
        List of office assets and their current status.
    </p>

    <table>
        <thead>
            <tr>
                <th>Asset ID</th>
                <th>Asset Name</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
';

if (empty($assets)) {

    $html .= '
        <tr>
            <td colspan="3" class="empty-state">
                No assets found.
            </td>
        </tr>
    ';
} else {

    foreach ($assets as $asset) {

        $status = strtolower((string) ($asset['status'] ?? ''));

        if ($status === '') {
            $status = 'available';
        }

        $html .= '
            <tr>
                <td>
                    #' . htmlspecialchars($asset['id'] ?? '') . '
                </td>

                <td>
                    ' . htmlspecialchars($asset['name'] ?? '') . '
                </td>

                <td>
                    <span class="badge">
                        ' . htmlspecialchars($asset['status'] ?? 'N/A') . '
                    </span>
                </td>
            </tr>
        ';
    }
}

$html .= '
        </tbody>
    </table>

    <div class="footer">
        Generated on ' . date('d M Y, h:i A') . '
    </div>

</body>
</html>
';

$dompdf = new Dompdf();

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream('assets.pdf', [
    'Attachment' => true
]);
