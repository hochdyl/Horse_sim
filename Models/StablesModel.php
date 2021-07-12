<?php

namespace App\Models;

use App\Core\System\Model;

class StablesModel extends Model {

    protected int $id;
    protected int $player_id;
    protected int $buildings_limit;
    protected float $price;
    protected int $on_sale;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return StablesModel
     */
    public function setId(int $id): StablesModel {
        $this->id = $id;
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
     * @return StablesModel
     */
    public function setPlayerId(int $player_id): StablesModel {
        $this->player_id = $player_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getBuildingsLimit(): int {
        return $this->buildings_limit;
    }

    /**
     * @param int $buildings_limit
     * @return StablesModel
     */
    public function setBuildingsLimit(int $buildings_limit): StablesModel {
        $this->buildings_limit = $buildings_limit;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float {
        return $this->price;
    }

    /**
     * @param float $price
     * @return StablesModel
     */
    public function setPrice(float $price): StablesModel {
        $this->price = $price;
        return $this;
    }

    /**
     * @return int
     */
    public function getOnSale(): int {
        return $this->on_sale;
    }

    /**
     * @param int $on_sale
     * @return StablesModel
     */
    public function setOnSale(int $on_sale): StablesModel {
        $this->on_sale = $on_sale;
        return $this;
    }

}
