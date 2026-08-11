<?php

use App\Controllers\Designation\CreateDesignationController;
use App\Controllers\Designation\SelectDesignationController;

switch ("$method:$route") {
    case 'GET:designations':
        (new SelectDesignationController($conn))->index($_GET);
        return true;

    case 'GET:designations/create':
        (new CreateDesignationController($conn))->create();
        return true;

    case 'POST:designations/create':
        (new CreateDesignationController($conn))->store($_POST);
        return true;
}

return false;