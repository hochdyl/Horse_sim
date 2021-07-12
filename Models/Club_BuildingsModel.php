<?php

namespace App\Models;

use App\Core\System\Model;

class Club_BuildingsModel extends Model {

    protected int $club_id;
    protected int $building_id;
    protected int $quantity;

    /**
     * @return int
     */
    public function getClubId(): int {
        return $this->club_id;
    }

    /**
     * @param int $club_id
     * @return Club_BuildingsModel
     */
    public function setClubId(int $club_id): Club_BuildingsModel {
        $this->club_id = $club_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getBuildingId(): int {
        return $this->building_id;
    }

    /**
     * @param int $building_id
     * @return Club_BuildingsModel
     */
    public function setBuildingId(int $building_id): Club_BuildingsModel {
        $this->building_id = $building_id;
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
     * @return Club_BuildingsModel
     */
    public function setQuantity(int $quantity): Club_BuildingsModel {
        $this->quantity = $quantity;
        return $this;
    }

}
