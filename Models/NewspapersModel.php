<?php

namespace App\Models;

use App\Core\System\Model;

class NewspapersModel extends Model {

    protected int $id;
    protected string $date;
    protected int $weather_id;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return NewspapersModel
     */
    public function setId(int $id): NewspapersModel {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getDate(): string {
        return $this->date;
    }

    /**
     * @param string $date
     * @return NewspapersModel
     */
    public function setDate(string $date): NewspapersModel {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int
     */
    public function getWeatherId(): int {
        return $this->weather_id;
    }

    /**
     * @param int $weather_id
     * @return NewspapersModel
     */
    public function setWeatherId(int $weather_id): NewspapersModel {
        $this->weather_id = $weather_id;
        return $this;
    }

}
