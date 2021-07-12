<?php

namespace App\Models;

use App\Core\System\Model;

class Club_MembersModel extends Model {

    protected int $club_id;
    protected int $player_id;

    /**
     * @return int
     */
    public function getClubId(): int {
        return $this->club_id;
    }

    /**
     * @param int $club_id
     * @return Club_MembersModel
     */
    public function setClubId(int $club_id): Club_MembersModel {
        $this->club_id = $club_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getPlayerId(): int {
        return $this->player_id;
    }

    /**
     * @param int $player_id
     * @return Club_MembersModel
     */
    public function setPlayerId(int $player_id): Club_MembersModel {
        $this->player_id = $player_id;
        return $this;
    }

}
