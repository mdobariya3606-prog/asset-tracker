<?php

switch ("$method:$route") {
    case 'GET:':
        route(empty($_SESSION['user_id']) ? 'login' : 'users');
        return true;
}

return false;