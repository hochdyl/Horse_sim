<?php

namespace App\Models;

use App\Core\System\Model;

class Building_TypesModel extends Model {

    protected int $id;
    protected string $name;
    protected int $items_limit;
    protected int $horses_limit;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Building_TypesModel
     */
    public function setId(int $id): Building_TypesModel {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Building_TypesModel
     */
    public function setName(string $name): Building_TypesModel {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getItemsLimit(): int {
        return $this->items_limit;
    }

    /**
     * @param int $items_limit
     * @return Building_TypesModel
     */
    public function setItemsLimit(int $items_limit): Building_TypesModel {
        $this->items_limit = $items_limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getHorsesLimit(): int {
        return $this->horses_limit;
    }

    /**
     * @param int $horses_limit
     * @return Building_TypesModel
     */
    public function setHorsesLimit(int $horses_limit): Building_TypesModel {
        $this->horses_limit = $horses_limit;
        return $this;
    }

}
