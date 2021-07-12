<?php

namespace App\Models;

use App\Core\System\Model;

class ClubsModel extends Model {

    protected int $id;
    protected int $player_id;
    protected int $buildings_limit;
    protected float $membership_fee;
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
     * @return ClubsModel
     */
    public function setId(int $id): ClubsModel {
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
     * @return ClubsModel
     */
    public function setPlayerId(int $player_id): ClubsModel {
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
     * @return ClubsModel
     */
    public function setBuildingsLimit(int $buildings_limit): ClubsModel {
        $this->buildings_limit = $buildings_limit;
        return $this;
    }

    /**
     * @return float
     */
    public function getMembershipFee(): float {
        return $this->membership_fee;
    }

    /**
     * @param float $membership_fee
     * @return ClubsModel
     */
    public function setMembershipFee(float $membership_fee): ClubsModel {
        $this->membership_fee = $membership_fee;
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
     * @return ClubsModel
     */
    public function setPrice(float $price): ClubsModel {
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
     * @return ClubsModel
     */
    public function setOnSale(int $on_sale): ClubsModel {
        $this->on_sale = $on_sale;
        return $this;
    }

}
