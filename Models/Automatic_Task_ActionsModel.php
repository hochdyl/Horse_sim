<?php

namespace App\Models;

use App\Core\System\Model;

class Automatic_Task_ActionsModel extends Model {

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
     * @return Automatic_Task_ActionsModel
     */
    public function setId(int $id): Automatic_Task_ActionsModel {
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
     * @return Automatic_Task_ActionsModel
     */
    public function setName(string $name): Automatic_Task_ActionsModel {
        $this->name = $name;
        return $this;
    }

}