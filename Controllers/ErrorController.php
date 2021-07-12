<?php

namespace App\Controllers;

use App\Core\System\Controller;
use JetBrains\PhpStorm\NoReturn;

final class ErrorController extends Controller {

    #[NoReturn] public static function error404() {
        http_response_code(404);
        (new ErrorController)->render(name_file: 'error/404', title: 'Erreur 404');
        die();
    }
}
