<?php

namespace App\Models;

use App\Core\System\Model;

class WeathersModel extends Model {

    protected int $id;
    protected string $name;
    protected string $description;
    protected string $image;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return WeathersModel
     */
    public function setId(int $id): WeathersModel {
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
     * @return WeathersModel
     */
    public function setName(string $name): WeathersModel {
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
     * @return WeathersModel
     */
    public function setDescription(string $description): WeathersModel {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): string {
        return $this->image;
    }

    /**
     * @param string $image
     * @return WeathersModel
     */
    public function setImage(string $image): WeathersModel {
        $this->image = $image;
        return $this;
    }

}
