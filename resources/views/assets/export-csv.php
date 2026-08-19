<?php

/** @var array $assets */

$status_default = 'Available';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="assets.csv"');
header('Cache-Control: max-age=0');

$output = fopen('php://output', 'w');

// UTF-8 BOM so Excel doesn't mangle special characters on open
fwrite($output, "\xEF\xBB\xBF");

fputcsv($output, [
    'Asset ID',
    'Asset Name',
    'Category',
    'Brand',
    'Model',
    'Cost',
    'Serial Number',
    'Vendor',
    'Status',
    'Assignee Id',
    'Purchase date',
    'Warranty date',
]);

foreach ($assets as $asset) {

    $status = $asset['status'] ?? '';
    if ($status === '') {
        $status = $status_default;
    }

    fputcsv($output, [
        $asset['id'] ?? 'N/A',
        $asset['name'] ?? 'N/A',
        $asset['category_name'] ?? 'N/A',
        $asset['brand'] ?? 'N/A',
        $asset['model'] ?? 'N/A',
        $asset['cost'] ?? 'N/A',
        $asset['serial_number'] ?? 'N/A',
        $asset['vendor_name'] ?? 'N/A',
        $status,
        $asset['assignee_id'] ?? 'N/A',
        $asset['purchase_date'] ?? 'N/A',
        $asset['warranty_date'] ?? 'N/A',
    ]);
}

fclose($output);
exit;
