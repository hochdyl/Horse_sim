<?php

namespace App\Models;

use App\Core\System\Model;

class Building_ItemsModel extends Model {

    protected int $building_id;
    protected int $item_id;
    protected int $quantity;

    /**
     * @return int
     */
    public function getBuildingId(): int {
        return $this->building_id;
    }

    /**
     * @param int $building_id
     * @return Building_ItemsModel
     */
    public function setBuildingId(int $building_id): Building_ItemsModel {
        $this->building_id = $building_id;
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
     * @return Building_ItemsModel
     */
    public function setItemId(int $item_id): Building_ItemsModel {
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
     * @return Building_ItemsModel
     */
    public function setQuantity(int $quantity): Building_ItemsModel {
        $this->quantity = $quantity;
        return $this;
    }

}
