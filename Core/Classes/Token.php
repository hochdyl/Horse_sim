<?php

namespace App\Core\Classes;

use Exception;

class Token {

    public static function generate(int $number = 64): string {
        try {
            return bin2hex(random_bytes($number));
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

}
