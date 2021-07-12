<?php

namespace App\Models;

use App\Core\System\Model;

class Club_Tournament_RegistrantsModel extends Model {

    protected int $club_tournament_id;
    protected int $player_id;
    protected int $rank;

    /**
     * @return int
     */
    public function getClubTournamentId(): int {
        return $this->club_tournament_id;
    }

    /**
     * @param int $club_tournament_id
     * @return Club_Tournament_RegistrantsModel
     */
    public function setClubTournamentId(int $club_tournament_id): Club_Tournament_RegistrantsModel {
        $this->club_tournament_id = $club_tournament_id;
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
     * @return Club_Tournament_RegistrantsModel
     */
    public function setPlayerId(int $player_id): Club_Tournament_RegistrantsModel {
        $this->player_id = $player_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getRank(): int {
        return $this->rank;
    }

    /**
     * @param int $rank
     * @return Club_Tournament_RegistrantsModel
     */
    public function setRank(int $rank): Club_Tournament_RegistrantsModel {
        $this->rank = $rank;
        return $this;
    }

}
