<?php

namespace App\Models;

use App\Core\System\Model;

class HorsesModel extends Model {

    protected int $id;
    protected string $name;
    protected int $breed_id;
    protected int $health;
    protected int $fatigue;
    protected int $morale;
    protected int $stress;
    protected int $hunger;
    protected int $cleanliness;
    protected int $global_condition;
    protected int $resistance;
    protected int $stamina;
    protected int $jump;
    protected int $speed;
    protected int $sociability;
    protected int $intelligence;
    protected int $temperament;
    protected int $experience;
    protected int $level;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return HorsesModel
     */
    public function setId(int $id): HorsesModel {
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
     * @return HorsesModel
     */
    public function setName(string $name): HorsesModel {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getBreedId(): int {
        return $this->breed_id;
    }

    /**
     * @param int $breed_id
     * @return HorsesModel
     */
    public function setBreedId(int $breed_id): HorsesModel {
        $this->breed_id = $breed_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getHealth(): int {
        return $this->health;
    }

    /**
     * @param int $health
     * @return HorsesModel
     */
    public function setHealth(int $health): HorsesModel {
        $this->health = $health;
        return $this;
    }

    /**
     * @return int
     */
    public function getFatigue(): int {
        return $this->fatigue;
    }

    /**
     * @param int $fatigue
     * @return HorsesModel
     */
    public function setFatigue(int $fatigue): HorsesModel {
        $this->fatigue = $fatigue;
        return $this;
    }

    /**
     * @return int
     */
    public function getMorale(): int {
        return $this->morale;
    }

    /**
     * @param int $morale
     * @return HorsesModel
     */
    public function setMorale(int $morale): HorsesModel {
        $this->morale = $morale;
        return $this;
    }

    /**
     * @return int
     */
    public function getStress(): int {
        return $this->stress;
    }

    /**
     * @param int $stress
     * @return HorsesModel
     */
    public function setStress(int $stress): HorsesModel {
        $this->stress = $stress;
        return $this;
    }

    /**
     * @return int
     */
    public function getHunger(): int {
        return $this->hunger;
    }

    /**
     * @param int $hunger
     * @return HorsesModel
     */
    public function setHunger(int $hunger): HorsesModel {
        $this->hunger = $hunger;
        return $this;
    }

    /**
     * @return int
     */
    public function getCleanliness(): int {
        return $this->cleanliness;
    }

    /**
     * @param int $cleanliness
     * @return HorsesModel
     */
    public function setCleanliness(int $cleanliness): HorsesModel {
        $this->cleanliness = $cleanliness;
        return $this;
    }

    /**
     * @return int
     */
    public function getGlobalCondition(): int {
        return $this->global_condition;
    }

    /**
     * @param int $global_condition
     * @return HorsesModel
     */
    public function setGlobalCondition(int $global_condition): HorsesModel {
        $this->global_condition = $global_condition;
        return $this;
    }

    /**
     * @return int
     */
    public function getResistance(): int {
        return $this->resistance;
    }

    /**
     * @param int $resistance
     * @return HorsesModel
     */
    public function setResistance(int $resistance): HorsesModel {
        $this->resistance = $resistance;
        return $this;
    }

    /**
     * @return int
     */
    public function getStamina(): int {
        return $this->stamina;
    }

    /**
     * @param int $stamina
     * @return HorsesModel
     */
    public function setStamina(int $stamina): HorsesModel {
        $this->stamina = $stamina;
        return $this;
    }

    /**
     * @return int
     */
    public function getJump(): int {
        return $this->jump;
    }

    /**
     * @param int $jump
     * @return HorsesModel
     */
    public function setJump(int $jump): HorsesModel {
        $this->jump = $jump;
        return $this;
    }

    /**
     * @return int
     */
    public function getSpeed(): int {
        return $this->speed;
    }

    /**
     * @param int $speed
     * @return HorsesModel
     */
    public function setSpeed(int $speed): HorsesModel {
        $this->speed = $speed;
        return $this;
    }

    /**
     * @return int
     */
    public function getSociability(): int {
        return $this->sociability;
    }

    /**
     * @param int $sociability
     * @return HorsesModel
     */
    public function setSociability(int $sociability): HorsesModel {
        $this->sociability = $sociability;
        return $this;
    }

    /**
     * @return int
     */
    public function getIntelligence(): int {
        return $this->intelligence;
    }

    /**
     * @param int $intelligence
     * @return HorsesModel
     */
    public function setIntelligence(int $intelligence): HorsesModel {
        $this->intelligence = $intelligence;
        return $this;
    }

    /**
     * @return int
     */
    public function getTemperament(): int {
        return $this->temperament;
    }

    /**
     * @param int $temperament
     * @return HorsesModel
     */
    public function setTemperament(int $temperament): HorsesModel {
        $this->temperament = $temperament;
        return $this;
    }

    /**
     * @return int
     */
    public function getExperience(): int {
        return $this->experience;
    }

    /**
     * @param int $experience
     * @return HorsesModel
     */
    public function setExperience(int $experience): HorsesModel {
        $this->experience = $experience;
        return $this;
    }

    /**
     * @return int
     */
    public function getLevel(): int {
        return $this->level;
    }

    /**
     * @param int $level
     * @return HorsesModel
     */
    public function setLevel(int $level): HorsesModel {
        $this->level = $level;
        return $this;
    }

}
