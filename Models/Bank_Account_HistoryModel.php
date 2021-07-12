<?php

namespace App\Models;

use App\Core\System\Model;

class Bank_Account_HistoryModel extends Model {

    protected int $id;
    protected int $bank_account_id;
    protected int $action;
    protected float $amount;
    protected string $label;
    protected string $date;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Bank_Account_HistoryModel
     */
    public function setId(int $id): Bank_Account_HistoryModel {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getBankAccountId(): int {
        return $this->bank_account_id;
    }

    /**
     * @param int $bank_account_id
     * @return Bank_Account_HistoryModel
     */
    public function setBankAccountId(int $bank_account_id): Bank_Account_HistoryModel {
        $this->bank_account_id = $bank_account_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getAction(): int {
        return $this->action;
    }

    /**
     * @param int $action
     * @return Bank_Account_HistoryModel
     */
    public function setAction(int $action): Bank_Account_HistoryModel {
        $this->action = $action;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return Bank_Account_HistoryModel
     */
    public function setAmount(float $amount): Bank_Account_HistoryModel {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }

    /**
     * @param string $label
     * @return Bank_Account_HistoryModel
     */
    public function setLabel(string $label): Bank_Account_HistoryModel {
        $this->label = $label;
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
     * @return Bank_Account_HistoryModel
     */
    public function setDate(string $date): Bank_Account_HistoryModel {
        $this->date = $date;
        return $this;
    }

}
