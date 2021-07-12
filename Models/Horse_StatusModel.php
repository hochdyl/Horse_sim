<?php

namespace App\Models;

use App\Core\System\Model;

class Horse_StatusModel extends Model {

    protected int $horse_id;
    protected int $status_id;
    protected string $onset_date;

    /**
     * @return int
     */
    public function getHorseId(): int {
        return $this->horse_id;
    }

    /**
     * @param int $horse_id
     * @return Horse_StatusModel
     */
    public function setHorseId(int $horse_id): Horse_StatusModel {
        $this->horse_id = $horse_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getStatusId(): int {
        return $this->status_id;
    }

    /**
     * @param int $status_id
     * @return Horse_StatusModel
     */
    public function setStatusId(int $status_id): Horse_StatusModel {
        $this->status_id = $status_id;
        return $this;
    }

    /**
     * @return string
     */
    public function getOnsetDate(): string {
        return $this->onset_date;
    }

    /**
     * @param string $onset_date
     * @return Horse_StatusModel
     */
    public function setOnsetDate(string $onset_date): Horse_StatusModel {
        $this->onset_date = $onset_date;
        return $this;
    }

}
