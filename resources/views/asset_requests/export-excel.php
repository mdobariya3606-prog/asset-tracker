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
    'Asset Name',
    'Reason',
    'Status',
    'Requested At',
    'Returned At',
    'Approved By',
    'Approved At',
    'Rejected By',
    'Rejected At',
    'Rejection Reason',
    'Issued By',
    'Issued At',
    'Remark'
], null, 'A1');

$row = 2;

if (!empty($requests)) {

    foreach ($requests as $request) {

        $sheet->fromArray([
            $request['id'] ?? '',
            $request['user_id'] ?? '',
            $request['asset_id'] ?? '',
            $request['asset_name'] ?? '',
            $request['reason'] ?? 'N/A',
            $request['status'] ?? 'N/A',

            $request['requested_at'] ?? 'N/A',
            $request['returned_at'] ?? 'N/A',

            $request['approved_by'] ?? 'N/A',
            $request['approved_at'] ?? 'N/A',

            $request['rejected_by'] ?? 'N/A',
            $request['rejected_at'] ?? 'N/A',
            $request['rejection_reason'] ?? 'N/A',

            $request['issued_by'] ?? 'N/A',
            $request['issued_at'] ?? 'N/A',

            $request['remark'] ?? 'N/A',

        ], null, 'A' . $row);

        $row++;
    }
}

// Header styling
$sheet->getStyle('A1:P1')->getFont()->setBold(true);

// Auto-size columns
foreach (range('A', 'P') as $column) {
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
