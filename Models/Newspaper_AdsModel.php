<?php

namespace App\Models;

use App\Core\System\Model;

class Newspaper_AdsModel extends Model {

    protected int $newspaper_id;
    protected int $ad_id;

    /**
     * @return int
     */
    public function getNewspaperId(): int {
        return $this->newspaper_id;
    }

    /**
     * @param int $newspaper_id
     * @return Newspaper_AdsModel
     */
    public function setNewspaperId(int $newspaper_id): Newspaper_AdsModel {
        $this->newspaper_id = $newspaper_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getAdId(): int {
        return $this->ad_id;
    }

    /**
     * @param int $ad_id
     * @return Newspaper_AdsModel
     */
    public function setAdId(int $ad_id): Newspaper_AdsModel {
        $this->ad_id = $ad_id;
        return $this;
    }

}
