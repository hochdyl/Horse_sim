<?php

namespace App\Models;

use App\Core\System\Model;

class Bank_AccountsModel extends Model {

    protected int $id;
    protected int $player_id;
    protected float $balance;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Bank_AccountsModel
     */
    public function setId(int $id): Bank_AccountsModel {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getPlayerId(): int {
        return $this->player_id;
    }

    /**
     * @param int $player_id
     * @return Bank_AccountsModel
     */
    public function setPlayerId(int $player_id): Bank_AccountsModel {
        $this->player_id = $player_id;
        return $this;
    }

    /**
     * @return float
     */
    public function getBalance(): float {
        return $this->balance;
    }

    /**
     * @param float $balance
     * @return Bank_AccountsModel
     */
    public function setBalance(float $balance): Bank_AccountsModel {
        $this->balance = $balance;
        return $this;
    }

}