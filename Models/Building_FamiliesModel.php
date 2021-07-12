<?php

namespace App\Models;

use App\Core\System\Model;

class Building_FamiliesModel extends Model {

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
     * @return Building_FamiliesModel
     */
    public function setId(int $id): Building_FamiliesModel {
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
     * @return Building_FamiliesModel
     */
    public function setName(string $name): Building_FamiliesModel {
        $this->name = $name;
        return $this;
    }

}
