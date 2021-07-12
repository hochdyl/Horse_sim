<?php

namespace App\Core\Classes\SuperGlobals;

use JetBrains\PhpStorm\Pure;

class Session implements StoreData {

    #[Pure] public function get(string $key): string|bool {
        return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : false;
    }

    public function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    public function delete(string $key = null, bool $restart_session = false): void {
        if (is_null($key)) {
            if (session_status() == PHP_SESSION_ACTIVE) session_destroy();
            if ($restart_session) session_start();
        } else {
            unset($_SESSION[$key]);
        }
    }

    public function exists(string $key): bool {
        return isset($_SESSION[$key]);
    }

}
