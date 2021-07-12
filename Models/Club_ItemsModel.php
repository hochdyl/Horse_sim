<?php

namespace App\Models;

use App\Core\System\Model;

class Club_ItemsModel extends Model {

    protected int $club_id;
    protected int $item_id;
    protected int $quantity;

    /**
     * @return int
     */
    public function getClubId(): int {
        return $this->club_id;
    }

    /**
     * @param int $club_id
     * @return Club_ItemsModel
     */
    public function setClubId(int $club_id): Club_ItemsModel {
        $this->club_id = $club_id;
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
     * @return Club_ItemsModel
     */
    public function setItemId(int $item_id): Club_ItemsModel {
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
     * @return Club_ItemsModel
     */
    public function setQuantity(int $quantity): Club_ItemsModel {
        $this->quantity = $quantity;
        return $this;
    }

}
