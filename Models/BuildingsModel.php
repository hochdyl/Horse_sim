<?php

namespace App\Models;

use App\Core\System\Model;

class BuildingsModel extends Model {

    protected int $id;
    protected int $building_type_id;
    protected int $building_family_id;
    protected string $description;
    protected int $level;
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
     * @return BuildingsModel
     */
    public function setId(int $id): BuildingsModel {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getBuildingTypeId(): int {
        return $this->building_type_id;
    }

    /**
     * @param int $building_type_id
     * @return BuildingsModel
     */
    public function setBuildingTypeId(int $building_type_id): BuildingsModel {
        $this->building_type_id = $building_type_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getBuildingFamilyId(): int {
        return $this->building_family_id;
    }

    /**
     * @param int $building_family_id
     * @return BuildingsModel
     */
    public function setBuildingFamilyId(int $building_family_id): BuildingsModel {
        $this->building_family_id = $building_family_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @param string $description
     * @return BuildingsModel
     */
    public function setDescription(string $description): BuildingsModel {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getLevel(): int {
        return $this->level;
    }

    /**
     * @param int $level
     * @return BuildingsModel
     */
    public function setLevel(int $level): BuildingsModel {
        $this->level = $level;
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
     * @return BuildingsModel
     */
    public function setPrice(float $price): BuildingsModel {
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
     * @return BuildingsModel
     */
    public function setOnSale(int $on_sale): BuildingsModel {
        $this->on_sale = $on_sale;
        return $this;
    }

}
