<?php

namespace App\Models;

use App\Core\System\Model;

class StatusesModel extends Model {

    protected int $id;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return StatusesModel
     */
    public function setId(int $id): StatusesModel {
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
     * @return StatusesModel
     */
    public function setName(string $name): StatusesModel {
        $this->name = $name;
        return $this;
    }
    protected string $name;

}
