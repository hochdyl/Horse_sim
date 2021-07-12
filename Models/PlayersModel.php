<?php

namespace App\Models;

use App\Core\System\Model;

class PlayersModel extends Model {

    protected int $id;
    protected string $nickname;
    protected string $mail;
    protected string $password;
    protected string $last_name;
    protected ?string $first_name;
    protected ?string $sexe;
    protected ?string $phone;
    protected ?string $street;
    protected ?string $city;
    protected ?string $zip_code;
    protected ?string $country;
    protected ?string $avatar;
    protected ?string $description;
    protected ?string $website;
    protected ?string $ip_address;
    protected ?string $register_datetime;
    protected ?string $log_in_datetime;

    /**
     * @return int
     */
    public function getId(): int {
        return $this->id;
    }

    /**
     * @param int $id
     * @return PlayersModel
     */
    public function setId(int $id): PlayersModel {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getNickname(): string {
        return $this->nickname;
    }

    /**
     * @param string $nickname
     * @return PlayersModel
     */
    public function setNickname(string $nickname): PlayersModel {
        $this->nickname = $nickname;
        return $this;
    }

    /**
     * @return string
     */
    public function getMail(): string {
        return $this->mail;
    }

    /**
     * @param string $mail
     * @return PlayersModel
     */
    public function setMail(string $mail): PlayersModel {
        $this->mail = $mail;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string {
        return $this->password;
    }

    /**
     * @param string $password
     * @return PlayersModel
     */
    public function setPassword(string $password): PlayersModel {
        $this->password = $password;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastName(): string {
        return $this->last_name;
    }

    /**
     * @param string $last_name
     * @return PlayersModel
     */
    public function setLastName(string $last_name): PlayersModel {
        $this->last_name = $last_name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string {
        return $this->first_name;
    }

    /**
     * @param string $first_name
     * @return PlayersModel
     */
    public function setFirstName(string $first_name): PlayersModel {
        $this->first_name = $first_name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSexe(): ?string {
        return $this->sexe;
    }

    /**
     * @param string $sexe
     * @return PlayersModel
     */
    public function setSexe(string $sexe): PlayersModel {
        $this->sexe = $sexe;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string {
        return $this->phone;
    }

    /**
     * @param string $phone
     * @return PlayersModel
     */
    public function setPhone(string $phone): PlayersModel {
        $this->phone = $phone;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStreet(): ?string {
        return $this->street;
    }

    /**
     * @param string $street
     * @return PlayersModel
     */
    public function setStreet(string $street): PlayersModel {
        $this->street = $street;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string {
        return $this->city;
    }

    /**
     * @param string $city
     * @return PlayersModel
     */
    public function setCity(string $city): PlayersModel {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string {
        return $this->zip_code;
    }

    /**
     * @param string $zip_code
     * @return PlayersModel
     */
    public function setZipCode(string $zip_code): PlayersModel {
        $this->zip_code = $zip_code;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string {
        return $this->country;
    }

    /**
     * @param string $country
     * @return PlayersModel
     */
    public function setCountry(string $country): PlayersModel {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAvatar(): ?string {
        return $this->avatar;
    }

    /**
     * @param string $avatar
     * @return PlayersModel
     */
    public function setAvatar(string $avatar): PlayersModel {
        $this->avatar = $avatar;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string {
        return $this->description;
    }

    /**
     * @param string $description
     * @return PlayersModel
     */
    public function setDescription(string $description): PlayersModel {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getWebsite(): ?string {
        return $this->website;
    }

    /**
     * @param string $website
     * @return PlayersModel
     */
    public function setWebsite(string $website): PlayersModel {
        $this->website = $website;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIpAddress(): ?string {
        return $this->ip_address;
    }

    /**
     * @param string $ip_address
     * @return PlayersModel
     */
    public function setIpAddress(string $ip_address): PlayersModel {
        $this->ip_address = $ip_address;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRegisterDatetime(): ?string {
        return $this->register_datetime;
    }

    /**
     * @param string $register_datetime
     * @return PlayersModel
     */
    public function setRegisterDatetime(string $register_datetime): PlayersModel {
        $this->register_datetime = $register_datetime;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLogInDatetime(): ?string {
        return $this->log_in_datetime;
    }

    /**
     * @param string $log_in_datetime
     * @return PlayersModel
     */
    public function setLogInDatetime(string $log_in_datetime): PlayersModel {
        $this->log_in_datetime = $log_in_datetime;
        return $this;
    }

}
