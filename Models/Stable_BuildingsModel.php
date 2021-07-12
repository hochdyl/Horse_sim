<?php

namespace App\Models;

use App\Core\System\Model;

class Stable_BuildingsModel extends Model {

    protected int $id;
    protected int $stable_id;
    protected int $building_id;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Stable_BuildingsModel
     */
    public function setId(int $id): Stable_BuildingsModel {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getStableId(): int {
        return $this->stable_id;
    }

    /**
     * @param int $stable_id
     * @return Stable_BuildingsModel
     */
    public function setStableId(int $stable_id): Stable_BuildingsModel {
        $this->stable_id = $stable_id;
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
     * @return Stable_BuildingsModel
     */
    public function setBuildingId(int $building_id): Stable_BuildingsModel {
        $this->building_id = $building_id;
        return $this;
    }

}
