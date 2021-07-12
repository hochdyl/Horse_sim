<?php

namespace App\Core\Classes\SuperGlobals;

use JetBrains\PhpStorm\Pure;

class Post implements StoreData {

    #[Pure] public function get(string $key): string|bool {
        return array_key_exists($key, $_POST) ? $_POST[$key] : false;
    }

    public function set(string $key, $value): void {
        $_POST[$key] = $value;
    }

    public function delete(string $key = null): void {
        unset($_POST[$key]);
    }

    public function exists(string $key): bool {
        return isset($_POST[$key]) || !empty($_POST[$key]);
    }

}
