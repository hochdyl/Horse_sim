<?php

namespace App\Models;

use App\Core\System\Model;

class UserModel extends Model {

    /**
     * @param string $user
     * @return bool|Model
     */
    public function findUser(string $user): bool|array|self {
        $db = $this->connect(DB_USER, DB_PASS, 'mysql');
        return $db->query("SELECT user FROM `user` WHERE user = '$user' LIMIT 1")->fetch();
    }

}
