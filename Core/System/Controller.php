<?php

namespace App\Core\System;

use App\Core\Classes\SuperGlobals\Request;
use App\Core\Routes\Route;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;

abstract class Controller {

    protected Request $request;

    public function __construct() {
        $this->request = new Request();

        if (session_status() == PHP_SESSION_NONE) session_start();

        if ($this->isAuthenticated()) $this->request->cookie->set('token', $this->request->session->get('token'));

        if (!$this->isAuthenticated() && $this->request->session->exists('token')) {
            $this->request->session->delete(restart_session: true);
            $this->addFlash('error', 'Vous avez été déconnectée pour inactivité !');
            $this->redirect(self::reverse('home'));
        }
    }

    protected function render(string $name_file, array $params = [], string $template = 'base', string $title = 'Accueil'): void {
        $start = microtime(true);

        $this->page($name_file, $template, $title, $params);

        $end = microtime(true);

        if (DEBUG) var_dump(round($end - $start, 5));
    }

    private function page(string $name_file, string $template, string $title, array $params): void {
        extract($params);

        ob_start();

        // On insère le fichier des messages flash
        require_once VIEWS . 'message/message.php';

        // On insère le fichier des fonctions utile pour la vue
        require_once __DIR__. '/functions.php';

        require_once VIEWS . "$name_file.php";

        isset($content) ?: $content = ob_get_clean();

        require_once VIEWS . "$template.php";
    }

    #[NoReturn] protected function redirect(string $header, bool $replace = false, int $response_code = 301): void {
        $new_header = ROOT . $header;

        if (str_contains($new_header, '//')) {
            $new_header = str_replace('//', '/', $new_header);
        }

        header("Location: $new_header", $replace, $response_code);
        die();
    }

    protected static function reverse(string $name, array $params = null): string
    {
        $path = array_search($name, Route::$name);

        if (!is_null($params)) {
            foreach ($params as $param) {
                $path = preg_replace('#{(int|str|all)}#', $param, $path, 1);
            }
        }

        return $path;
    }

    protected function addFlash(string $alert_type, string|array $message): void {
        $this->request->session->set($alert_type, $message);
    }

    #[Pure] protected function isAuthenticated(): bool {
        return $this->request->session->exists('token');
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

    protected function getActualUri(string $path): string {
        $port = empty($_SERVER['HTTPS']) ? 'http' : 'https';
        return $port . '://' . $_SERVER['HTTP_HOST'] . ROOT . $path;
    }

    protected function getGetter(object $data): array|bool {
        $list_method = [];

        foreach (get_class_methods($data) as $method) {
            if (str_starts_with($method, 'get')) {
                $list_method[
                strtolower(preg_replace('#([A-Z])#', '_$1', lcfirst(str_replace("get", "", $method))))
                ] = call_user_func_array([$data, $method], []);
            }
        }

        return $list_method;
    }

}
