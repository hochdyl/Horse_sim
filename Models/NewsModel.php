<?php

namespace App\Models;

use App\Core\System\Model;

class NewsModel extends Model {

    protected int $id;
    protected int $player_id;
    protected string $date;
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
     * @return NewsModel
     */
    public function setId(int $id): NewsModel {
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
     * @return NewsModel
     */
    public function setPlayerId(int $player_id): NewsModel {
        $this->player_id = $player_id;
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
     * @return NewsModel
     */
    public function setDate(string $date): NewsModel {
        $this->date = $date;
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
     * @return NewsModel
     */
    public function setName(string $name): NewsModel {
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
     * @return NewsModel
     */
    public function setDescription(string $description): NewsModel {
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
     * @return NewsModel
     */
    public function setImage(string $image): NewsModel {
        $this->image = $image;
        return $this;
    }

}
