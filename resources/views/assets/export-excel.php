<?php

require_once '../vendor/autoload.php';

use FontLib\Font;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Assets');

$sheet->fromArray([
    'Asset ID',
    'Asset Name',
    'Brand',
    'Model',
    'Cost',
    'Serial Number',
    'Vendor',
    'Status',
    'Assignee Id',
    'Purchase date',
    'Warranty date',
], null, 'A1');

$row = 2;
/** @var array $assets */

foreach ($assets as $asset) {

    $status = $asset['status'] ?? '';

    if ($status === '') {
        $status = 'Available';
    }

    $sheet->fromArray([
        $asset['id'] ?? 'N/A',
        $asset['name'] ?? 'N/A',
        $asset['brand'] ?? 'N/A',
        $asset['model'] ?? 'N/A',
        $asset['cost'] ?? 'N/A',
        $asset['serial_number'] ?? 'N/A',
        $asset['vendor_name'] ?? 'N/A',
        $asset['status'] ?? 'N/A',
        $asset['assignee_id'] ?? 'N/A',
        $asset['purchase_date'] ?? 'N/A',
        $asset['warranty_date'] ?? 'N/A',
    ], null, 'A' . $row);

    $row++;
}

// Header styling
$sheet->getStyle('A1:K1')->getFont()->setBold(true);

// Auto-size columns
foreach (range('A', 'C') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

// Create Excel file
$writer = new Xlsx($spreadsheet);

header(
    'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
);

header(
    'Content-Disposition: attachment; filename="assets.xlsx"'
);

header('Cache-Control: max-age=0');

$writer->save('php://output');

exit;
