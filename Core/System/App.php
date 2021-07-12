<?php


namespace App\Core\System;


use App\Core\Attributes\Route;
use App\Core\Routes\Router;
use ReflectionClass;

abstract class App
{
    public static function run()
    {
        // Afficher les erreurs si le DEBUG est activé
        switch (DEBUG) {
            case true:
                error_reporting(E_ALL);
                ini_set("display_errors", 1);
                break;
            default:
                error_reporting(0);
                ini_set("display_errors", 0);
        }

        $router = new Router($_GET['url']);

        foreach (scandir(__DIR__ . '/../../Controllers') as $controller) {
            if (preg_match('/Controller.php/', $controller)) {
                $controller = rtrim($controller, '.php');
                $reflector = new ReflectionClass("App\\Controllers\\$controller");

                $arguments = [];

                foreach ($reflector->getAttributes() as $attribute) {
                    $arguments = $attribute->getArguments();
                }

                foreach ($reflector->getMethods() as $method) {
                    foreach ($method->getAttributes(Route::class) as $attribute) {
                        $path_argument = empty($arguments) ? '' : $arguments[0];
                        $name_argument = $attribute->getArguments()[1] ?: '';
                        $method_argument = $attribute->getArguments()[2] ?? 'GET';

                        $router->add($path_argument . $attribute->getArguments()[0], "$method->class::$method->name", $name_argument, $method_argument);
                    }
                }
            }
        }

        $router->run();
    }
}