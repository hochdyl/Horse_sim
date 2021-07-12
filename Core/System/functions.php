<?php

use App\Core\Classes\SuperGlobals\Request;
use JetBrains\PhpStorm\Pure;

#[Pure] function addJavaScript(string $path, string $url = null): string|bool {
    if (!is_null($url)) {
        if ($_SERVER['REQUEST_URI'] === ROOT . $url) {
            return (str_starts_with($path, 'http')) ? "<script src=$path></script>" : "<script src=" . SCRIPTS ."$path></script>";
        }

        return false;
    }

    return (str_starts_with($path, 'http')) ? "<script src=$path></script>" : "<script src=" . SCRIPTS ."$path></script>";
}

#[Pure] function isAuthenticated(): bool {
    $request = new Request();
    return $request->session->exists('token');
}

#[Pure] function permissions(string $value, array $permissions): bool {
    if (in_array($value, $permissions)) {
        return true;
    } elseif ((in_array("*", $permissions))) {
        return true;
    } elseif ((in_array("ALL PRIVILEGES", $permissions))) {
        return true;
    } else {
        return false;
    }
}