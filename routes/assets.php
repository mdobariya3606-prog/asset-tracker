<?php

use App\Controllers\Asset\CreateAssetController;
use App\Controllers\Asset\SelectAssetController;
use App\Models\Asset;

switch ("$method:$route") {
    case 'GET:assets':
        (new SelectAssetController($conn))->index();
        return true;

    case 'GET:assets/show':
        (new SelectAssetController($conn))->show((int)($_GET['id'] ?? 0));
        return true;

    case 'GET:assets/create':
        (new CreateAssetController($conn))->create();
        return true;

    case 'POST:assets/create':
        (new CreateAssetController($conn))->store($_POST);
        return true;

    case 'GET:assets/edit':
        (new CreateAssetController($conn))->edit((int)($_GET['id'] ?? 0));
        return true;

    case 'POST:assets/edit':
        (new CreateAssetController($conn))->update((int)($_GET['id'] ?? 0), $_POST);
        return true;

    case 'GET:assets/delete':
        (new CreateAssetController($conn))->delete((int)($_GET['id'] ?? 0));
        return true;

    case 'GET:assets/invoice':
        (new Asset($conn))->print($_GET['id'], 'invoice');
        return true;

    case 'GET:assets/warranty':
        (new Asset($conn))->print($_GET['id'], 'warranty');
        return true;
}

return false;
