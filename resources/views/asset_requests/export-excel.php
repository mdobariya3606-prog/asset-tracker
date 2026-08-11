<?php

require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Asset Requests');

$sheet->fromArray([
    'Request ID',
    'User ID',
    'Asset ID',
    'Status',
    'Requested At'
], null, 'A1');

$row = 2;

if (!empty($requests)) {

    foreach ($requests as $request) {

        $sheet->fromArray([
            $request['id'] ?? '',
            $request['user_id'] ?? '',
            $request['asset_id'] ?? '',
            $request['status'] ?? 'N/A',
            $request['requested_at'] ?? '',
        ], null, 'A' . $row);

        $row++;
    }
}

// Header styling
$sheet->getStyle('A1:E1')->getFont()->setBold(true);

// Auto-size columns
foreach (range('A', 'E') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);

header(
    'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
);

header(
    'Content-Disposition: attachment; filename="asset-requests.xlsx"'
);

header('Cache-Control: max-age=0');

$writer->save('php://output');

exit;
