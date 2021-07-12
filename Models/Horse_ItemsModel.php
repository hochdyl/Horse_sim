<?php

namespace App\Models;

use App\Core\System\Model;

class Horse_ItemsModel extends Model {

    protected int $horse_id;
    protected int $item_id;
    protected int $quantity;

    /**
     * @return int
     */
    public function getHorseId(): int
    {
        return $this->horse_id;
    }

    /**
     * @param int $horse_id
     * @return Horse_ItemsModel
     */
    public function setHorseId(int $horse_id): Horse_ItemsModel
    {
        $this->horse_id = $horse_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getItemId(): int
    {
        return $this->item_id;
    }

    /**
     * @param int $item_id
     * @return Horse_ItemsModel
     */
    public function setItemId(int $item_id): Horse_ItemsModel
    {
        $this->item_id = $item_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return Horse_ItemsModel
     */
    public function setQuantity(int $quantity): Horse_ItemsModel
    {
        $this->quantity = $quantity;
        return $this;
    }

}
