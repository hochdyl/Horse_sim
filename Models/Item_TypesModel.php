<?php

namespace App\Models;

use App\Core\System\Model;

class Item_TypesModel extends Model {

    protected int $id;
    protected string $name;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): Item_TypesModel {
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
     */
    public function setName(string $name): Item_TypesModel {
        $this->name = $name;
        return $this;
    }

}
