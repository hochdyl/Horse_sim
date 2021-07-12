<?php

namespace App\Models;

use App\Core\System\Model;

class ItemsModel extends Model {

    protected int $id;
    protected int $item_type_id;
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
     * @return ItemsModel
     */
    public function setId(int $id): ItemsModel {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getItemTypeId(): int {
        return $this->item_type_id;
    }

    /**
     * @param int $item_type_id
     * @return ItemsModel
     */
    public function setItemTypeId(int $item_type_id): ItemsModel {
        $this->item_type_id = $item_type_id;
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
     * @return ItemsModel
     */
    public function setDescription(string $description): ItemsModel {
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
     * @return ItemsModel
     */
    public function setLevel(int $level): ItemsModel {
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
     * @return ItemsModel
     */
    public function setPrice(float $price): ItemsModel {
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
     * @return ItemsModel
     */
    public function setOnSale(int $on_sale): ItemsModel {
        $this->on_sale = $on_sale;
        return $this;
    }

}
