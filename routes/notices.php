<?php

use App\Controllers\Notice\CreateNoticeController;
use App\Controllers\Notice\SelectNoticeController;
use App\Models\Notice;

switch ("$method:$route") {
    case 'GET:notices':
        (new SelectNoticeController($conn))->index();
        return true;

    case 'GET:notices/create':
        (new CreateNoticeController($conn))->create();
        return true;

    case 'POST:notices/create':
        (new CreateNoticeController($conn))->store($_POST);
        return true;

    case 'GET:notices/edit':
        (new \App\Controllers\Notice\EditNoticeController($conn))->edit($_GET);
        return true;

    case 'POST:notices/edit':
        (new \App\Controllers\Notice\EditNoticeController($conn))->update($_GET, $_POST);
        return true;

    case 'POST:notices/delete':
        (new \App\Controllers\Notice\EditNoticeController($conn))->destroy($_GET);
        return true;

    case 'GET:notices/mark-confirmed':
        (new Notice($conn))->markConfirmed();
        return true;
}
return false;
