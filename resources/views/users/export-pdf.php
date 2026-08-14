<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use Dompdf\Dompdf;

// Fetch $users here if they are not already available.
// $users = ...

$css = file_get_contents('../resources/css/pdf.css');

$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Users Directory</title>

    <style>
        ' . $css . '
    </style>
</head>

<body>

    <h1>Users Directory</h1>

    <p class="subtitle">
        List of registered users
    </p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User Name</th>
                <th>Contact / Mobile</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Role</th>
            </tr>
        </thead>

        <tbody>
';

if (empty($users)) {

    $html .= '
        <tr>
            <td colspan="6" class="empty-state">
                No users found.
            </td>
        </tr>
    ';
} else {

    foreach ($users as $user) {

        $html .= '
            <tr>
                <td>
                    #' . htmlspecialchars($user['id'] ?? '') . '
                </td>

                <td>
                    <strong>
                        ' . htmlspecialchars($user['name'] ?? '') . '
                    </strong>

                    <br>

                    <span class="email">
                        ' . htmlspecialchars($user['email'] ?? '') . '
                    </span>
                </td>

                <td>
                    ' . htmlspecialchars($user['mobile'] ?? 'N/A') . '
                </td>

                <td>
                    ' . htmlspecialchars($user['department_name'] ?? 'N/A') . '
                </td>

                <td>
                    ' . htmlspecialchars($user['designation_name'] ?? 'N/A') . '
                </td>

                <td>
                    <span class="badge">
                        ' . htmlspecialchars($user['role'] ?? 'EMPLOYEE') . '
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

$dompdf->setPaper('A4', 'landscape');

$dompdf->render();

$dompdf->stream('users.pdf', [
    'Attachment' => true
]);
