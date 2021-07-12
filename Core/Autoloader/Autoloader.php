<?php

namespace App\Core\Autoloader;

abstract class Autoloader {

    private static array $replace = array(
        __NAMESPACE__ . '\\' => '',
        '\\' => '/',
        'App/' => ''
    );

    public static function register() {
        spl_autoload_register([
            __CLASS__, 'autoload'
        ]);
    }

    private static function autoload(string $class) {
        $class = str_replace(array_keys(self::$replace), self::$replace, $class);

        if (file_exists(dirname(__DIR__, 2) . "/$class.php"))
            require_once dirname(__DIR__, 2) . "/$class.php";
    }

}
