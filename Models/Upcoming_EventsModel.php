<?php

namespace App\Models;

use App\Core\System\Model;

class Upcoming_EventsModel extends Model {

    protected int $id;
    protected int $newspaper_id;
    protected string $name;
    protected string $description;
    protected string $image;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Upcoming_EventsModel
     */
    public function setId(int $id): Upcoming_EventsModel {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getNewspaperId(): int {
        return $this->newspaper_id;
    }

    /**
     * @param int $newspaper_id
     * @return Upcoming_EventsModel
     */
    public function setNewspaperId(int $newspaper_id): Upcoming_EventsModel {
        $this->newspaper_id = $newspaper_id;
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
     * @return Upcoming_EventsModel
     */
    public function setName(string $name): Upcoming_EventsModel {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Upcoming_EventsModel
     */
    public function setDescription(string $description): Upcoming_EventsModel {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): string {
        return $this->image;
    }

    /**
     * @param string $image
     * @return Upcoming_EventsModel
     */
    public function setImage(string $image): Upcoming_EventsModel {
        $this->image = $image;
        return $this;
    }

}
