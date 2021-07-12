<?php

namespace App\Models;

use App\Core\System\Model;

class Automatic_TasksModel extends Model {

    protected int $id;
    protected int $automatic_task_action_id;
    protected int $stable_building_id;
    protected int $item_id;
    protected int $frequency;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Automatic_TasksModel
     */
    public function setId(int $id): Automatic_TasksModel {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getAutomaticTaskActionId(): int {
        return $this->automatic_task_action_id;
    }

    /**
     * @param int $automatic_task_action_id
     * @return Automatic_TasksModel
     */
    public function setAutomaticTaskActionId(int $automatic_task_action_id): Automatic_TasksModel {
        $this->automatic_task_action_id = $automatic_task_action_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getStableBuildingId(): int {
        return $this->stable_building_id;
    }

    /**
     * @param int $stable_building_id
     * @return Automatic_TasksModel
     */
    public function setStableBuildingId(int $stable_building_id): Automatic_TasksModel {
        $this->stable_building_id = $stable_building_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getItemId(): int {
        return $this->item_id;
    }

    /**
     * @param int $item_id
     * @return Automatic_TasksModel
     */
    public function setItemId(int $item_id): Automatic_TasksModel {
        $this->item_id = $item_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getFrequency(): int {
        return $this->frequency;
    }

    /**
     * @param int $frequency
     * @return Automatic_TasksModel
     */
    public function setFrequency(int $frequency): Automatic_TasksModel {
        $this->frequency = $frequency;
        return $this;
    }

}
