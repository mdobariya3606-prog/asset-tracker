<?php

use App\Controllers\Department\CreateDepartmentController;
use App\Controllers\Department\SelectDepartmentController;

switch ("$method:$route") {
    case 'GET:departments':
        (new SelectDepartmentController($conn))->index($_GET);
        return true;

    case 'GET:departments/create':
        (new CreateDepartmentController($conn))->create();
        return true;

    case 'POST:departments/create':
        (new CreateDepartmentController($conn))->store($_POST);
        return true;
}

return false;