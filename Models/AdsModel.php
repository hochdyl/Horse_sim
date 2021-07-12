<?php

namespace App\Models;

use App\Core\System\Model;

class AdsModel extends Model {

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
     * @return AdsModel
     */
    public function setId(int $id): AdsModel {
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
     * @return AdsModel
     */
    public function setName(string $name): AdsModel {
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
     * @return AdsModel
     */
    public function setDescription(string $description): AdsModel {
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
     * @return AdsModel
     */
    public function setImage(string $image): AdsModel {
        $this->image = $image;
        return $this;
    }

}
