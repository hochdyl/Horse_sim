<?php

namespace App\Models;

use App\Core\System\Model;

class Club_TournamentsModel extends Model {

    protected int $id;
    protected int $club_id;
    protected string $name;
    protected string $start_date;
    protected string $end_date;
    protected float $base_registration_fee;
    protected float $member_registration_fee;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Club_TournamentsModel
     */
    public function setId(int $id): Club_TournamentsModel {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getClubId(): int {
        return $this->club_id;
    }

    /**
     * @param int $club_id
     * @return Club_TournamentsModel
     */
    public function setClubId(int $club_id): Club_TournamentsModel {
        $this->club_id = $club_id;
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
     * @return Club_TournamentsModel
     */
    public function setName(string $name): Club_TournamentsModel {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getStartDate(): string {
        return $this->start_date;
    }

    /**
     * @param string $start_date
     * @return Club_TournamentsModel
     */
    public function setStartDate(string $start_date): Club_TournamentsModel {
        $this->start_date = $start_date;
        return $this;
    }

    /**
     * @return string
     */
    public function getEndDate(): string {
        return $this->end_date;
    }

    /**
     * @param string $end_date
     * @return Club_TournamentsModel
     */
    public function setEndDate(string $end_date): Club_TournamentsModel {
        $this->end_date = $end_date;
        return $this;
    }

    /**
     * @return float
     */
    public function getBaseRegistrationFee(): float {
        return $this->base_registration_fee;
    }

    /**
     * @param float $base_registration_fee
     * @return Club_TournamentsModel
     */
    public function setBaseRegistrationFee(float $base_registration_fee): Club_TournamentsModel {
        $this->base_registration_fee = $base_registration_fee;
        return $this;
    }

    /**
     * @return float
     */
    public function getMemberRegistrationFee(): float {
        return $this->member_registration_fee;
    }

    /**
     * @param float $member_registration_fee
     * @return Club_TournamentsModel
     */
    public function setMemberRegistrationFee(float $member_registration_fee): Club_TournamentsModel {
        $this->member_registration_fee = $member_registration_fee;
        return $this;
    }

}
