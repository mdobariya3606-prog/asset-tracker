<?php

use App\Models\Asset;
use App\Models\AssetRequest;
use App\Models\User;

switch ("$method:$route") {
    case 'GET:assets/pdf':
        (new Asset($conn))->export('pdf');
        return true;

    case 'GET:assets/excel':
        (new Asset($conn))->export('excel');
        return true;

    case 'GET:assets/requests/pdf':
        (new AssetRequest($conn))->export('pdf');
        return true;

    case 'GET:assets/requests/excel':
        (new AssetRequest($conn))->export('excel');
        return true;

    case 'GET:users/pdf':
        (new User($conn))->export('pdf');
        return true;

    case 'GET:users/excel':
        (new User($conn))->export('excel');
        return true;

    case 'GET:users/employees/pdf':
        (new User($conn))->export('pdf', 'employee');
        return true;

    case 'GET:users/employees/excel':
        (new User($conn))->export('excel', 'employee');
        return true;
}

return false;