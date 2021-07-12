<?php


namespace App\Core\Routes;


use JetBrains\PhpStorm\Pure;
use App\Core\Exceptions\RouterException;

final class Router
{
    private string $url;
    private array $routes = [];
    private const METHOD = array(
        'GET', 'POST', 'HEAD', 'PUT', 'DELETE'
    );

    #[Pure] public function __construct(string $url)
    {
        $this->url = self::cleanUrl($url);
    }

    public function add(string $path, string $action, string $name, string|array $method): void
    {
        if (is_array($method)) {
            foreach ($method as $value) {
                $method = strtoupper($value);
                $this->routes[$method][] = new Route($path, $action, $name);
            }
        }

        $method = strtoupper($method);

        if (is_string($method)) {
            $this->routes[$method][] = new Route($path, $action, $name);
        }
    }

    /**
     * @return mixed
     * @throws RouterException
     */
    private function prepare(): mixed
    {
        foreach (array_keys($this->routes) as $method) {
            if (!in_array($method, self::METHOD)) {
                throw new RouterException("The request method <u>$method</u> doesn't exists !", 2);
            }
        }

        if (!in_array($_SERVER['REQUEST_METHOD'], array_keys($this->routes))) {
            throw new RouterException("This request method is not allowed there !", 3);
        }

        foreach ($this->routes[$_SERVER['REQUEST_METHOD']] ?? [''] as $route) {
            if (!empty($route) && $route->match($this->url)) {
                return $route->execute();
            }
        }

        throw new RouterException("No matching routes !", 4);
    }

    public function run() {
        try {
            $this->prepare();
        } catch (RouterException $e) {
            if (DEBUG) echo 'Error : ' . $e->getMessage();
            $e->error404();
        }
    }

    #[Pure] public static function cleanUrl(string $url): string
    {
        $url = trim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return $url;
    }
}