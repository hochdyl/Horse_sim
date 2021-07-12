<?php

namespace App\Core\Exceptions;

use App\Controllers\ErrorController;
use Exception;
use JetBrains\PhpStorm\NoReturn;

final class RouterException extends Exception {

    #[NoReturn] public function error404() {
        ErrorController::error404();
    }

}
