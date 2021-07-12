<?php

namespace App\Core\Classes\SuperGlobals;

interface StoreData {

    public function get(string $key);
    public function delete(string $key);
    public function exists(string $key);

}
