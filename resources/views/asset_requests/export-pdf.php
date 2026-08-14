<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Dompdf\Dompdf;

// Fetch requests here if they are not already available.
// $requests = ...

$css = file_get_contents('resources/css/pdf.css');

$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Asset Requests</title>

    <style>
        ' . $css . '
    </style>
</head>

<body>

    <h1>Asset Requests</h1>

    <p class="subtitle">
        List of asset requests
    </p>

    <table>
        <thead>
            <tr>
                <th>Request ID</th>
                <th>User ID</th>
                <th>Asset ID</th>
                <th>Status</th>
                <th>Requested At</th>
            </tr>
        </thead>

        <tbody>
';

if (empty($requests)) {

    $html .= '
        <tr>
            <td colspan="5" class="empty-state">
                No requests found.
            </td>
        </tr>
    ';
} else {

    foreach ($requests as $request) {

        $status = htmlspecialchars($request['status'] ?? 'N/A');

        $html .= '
            <tr>
                <td>
                    #' . htmlspecialchars($request['id'] ?? '') . '
                </td>

                <td>
                    #' . htmlspecialchars($request['user_id'] ?? '') . '
                </td>

                <td>
                    #' . htmlspecialchars($request['asset_id'] ?? '') . '
                </td>

                <td>
                    <span class="badge">
                        ' . $status . '
                    </span>
                </td>

                <td>
                    ' . htmlspecialchars($request['requested_at'] ?? '') . '
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

$dompdf->stream('asset-requests.pdf', [
    'Attachment' => true
]);
