<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Users');

$sheet->fromArray([
    'ID',
    'User Name',
    'Email',
    'Mobile',
    'Department',
    'Designation',
    'Role'
], null, 'A1');

$row = 2;

/** @var array $users */

foreach ($users as $user) {
    $sheet->fromArray([
        $user['id'] ?? '',
        $user['name'] ?? '',
        $user['email'] ?? '',
        $user['mobile'] ?? '',
        $user['department_name'] ?? 'N/A',
        $user['designation_name'] ?? 'N/A',
        $user['role'] ?? 'EMPLOYEE',
    ], null, 'A' . $row);

    $row++;
}

$sheet->getStyle('A1:G1')->getFont()->setBold(true);

foreach (range('A', 'G') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

$writer = new Xlsx($spreadsheet);

header(
    'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
);

header(
    'Content-Disposition: attachment; filename="users.xlsx"'
);

header('Cache-Control: max-age=0');

$writer->save('php://output');

exit;