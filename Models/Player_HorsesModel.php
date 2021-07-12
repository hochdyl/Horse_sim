<?php

namespace App\Models;

use App\Core\System\Model;

class Player_HorsesModel extends Model {

    protected int $player_id;
    protected int $horse_id;

    /**
     * @return int
     */
    public function getPlayerId(): int {
        return $this->player_id;
    }

    /**
     * @param int $player_id
     * @return Player_HorsesModel
     */
    public function setPlayerId(int $player_id): Player_HorsesModel {
        $this->player_id = $player_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getHorseId(): int {
        return $this->horse_id;
    }

    /**
     * @param int $horse_id
     * @return Player_HorsesModel
     */
    public function setHorseId(int $horse_id): Player_HorsesModel {
        $this->horse_id = $horse_id;
        return $this;
    }

}
