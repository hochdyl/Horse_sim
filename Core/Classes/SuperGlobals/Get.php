<?php

namespace App\Core\Classes\SuperGlobals;

use JetBrains\PhpStorm\Pure;

class Get implements StoreData {

    #[Pure] public function get(string $key): string|bool {
        return array_key_exists($key, $_GET) ? $_GET[$key] : false;
    }

    public function set(string $key, $value): void {
        $_GET[$key] = $value;
    }

    public function delete(string $key = null): void {
        unset($_GET[$key]);
    }

    public function exists(string $key): bool {
        return isset($_GET[$key]) || !empty($_GET[$key]);
    }

}
