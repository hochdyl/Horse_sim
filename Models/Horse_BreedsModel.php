<?php

namespace App\Models;

use App\Core\System\Model;

class Horse_BreedsModel extends Model {

    protected int $id;
    protected string $name;
    protected string $description;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Horse_BreedsModel
     */
    public function setId(int $id): Horse_BreedsModel {
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
     * @return Horse_BreedsModel
     */
    public function setName(string $name): Horse_BreedsModel {
        $this->name = $name;
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
     * @return Horse_BreedsModel
     */
    public function setDescription(string $description): Horse_BreedsModel {
        $this->description = $description;
        return $this;
    }

}
