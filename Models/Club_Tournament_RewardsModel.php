<?php

namespace App\Models;

use App\Core\System\Model;

class Club_Tournament_RewardsModel extends Model {

    protected int $id;
    protected int $club_tournament_id;
    protected int $item_id;
    protected int $quantity;
    protected int $obtention_rank;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Club_Tournament_RewardsModel
     */
    public function setId(int $id): Club_Tournament_RewardsModel {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getClubTournamentId(): int {
        return $this->club_tournament_id;
    }

    /**
     * @param int $club_tournament_id
     * @return Club_Tournament_RewardsModel
     */
    public function setClubTournamentId(int $club_tournament_id): Club_Tournament_RewardsModel {
        $this->club_tournament_id = $club_tournament_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getItemId(): int {
        return $this->item_id;
    }

    /**
     * @param int $item_id
     * @return Club_Tournament_RewardsModel
     */
    public function setItemId(int $item_id): Club_Tournament_RewardsModel {
        $this->item_id = $item_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return Club_Tournament_RewardsModel
     */
    public function setQuantity(int $quantity): Club_Tournament_RewardsModel {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return int
     */
    public function getObtentionRank(): int {
        return $this->obtention_rank;
    }

    /**
     * @param int $obtention_rank
     * @return Club_Tournament_RewardsModel
     */
    public function setObtentionRank(int $obtention_rank): Club_Tournament_RewardsModel {
        $this->obtention_rank = $obtention_rank;
        return $this;
    }

}
