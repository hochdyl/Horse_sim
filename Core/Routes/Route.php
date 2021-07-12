<?php


namespace App\Core\Routes;


use App\Core\Classes\SuperGlobals\Request;
use App\Core\Exceptions\RouterException;

final class Route
{
    public static array $name = [];
    private string $path;
    private array $matches;
    private const PATTERN = array(
        '#{(int)}#' => '([\d]+)',
        '#{(str)}#' => '([\D]+)',
        '#{(all)}#' => '([\w]+)'
    );

    public function __construct(
        string $path,
        private string $action,
        string $name
    )
    {
        $this->path = Router::cleanUrl($path);
        self::$name[$path] = $name;
    }

    public function match(string $url): bool
    {
        $path = preg_replace(array_keys(self::PATTERN), self::PATTERN, $this->path);
        $regex = "#^$path$#";

        if (!preg_match($regex, $url, $matches)) {
            return false;
        }

        $this->matches = $matches;
        return true;
    }

    /**
     * @throws RouterException
     */
    public function execute(): void
    {
        $params = explode('::', $this->action);

        $controller = new $params[0]();
        $method = $params[1];

        if (!method_exists($controller, $method)) {
            throw new RouterException("This method doesn't exist !", 1);
        }

        array_splice($this->matches, 0, 1);
        array_push($this->matches, new Request());

        isset($this->matches) ? call_user_func_array(array($controller, $method), $this->matches) : call_user_func_array(array($controller, $method), array(new Request()));
    }
}