<?php

use App\Controllers\Asset_request\ManageRequestController;
use App\Controllers\Asset_request\RequestAssetController;
use App\Controllers\Asset_request\SelectAssetRequestController;
use App\Models\AssetRequest;

switch ("$method:$route") {
    case 'GET:assets/requests':
        (new SelectAssetRequestController($conn))->index();
        return true;

    case 'GET:assets/requests/show':
        (new SelectAssetRequestController($conn))->show((int)($_GET['id'] ?? 0));
        return true;

    case 'GET:assets/requests/manage':
        (new ManageRequestController($conn))->showManageForm((int)($_GET['id'] ?? 0));
        return true;

    case 'POST:assets/requests/manage':
        (new ManageRequestController($conn))->update((int)($_GET['id'] ?? 0), $_POST);
        return true;

    case 'GET:assets/request':
        (new AssetRequest($conn))->create($_GET['id']);
        return true;

    case 'POST:assets/request':
        (new RequestAssetController($conn))->store($_GET['id'], $_POST);
        return true;

    case 'GET:assets/requests/cancel':
        (new ManageRequestController($conn))->cancel();
        return true;

    case 'GET:assets/requests/overdue':
        require __DIR__ . '/../public/testing/overdue.php';
        return true;
}

return false;
