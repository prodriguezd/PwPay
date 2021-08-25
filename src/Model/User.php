<?php

declare(strict_types=1);

namespace pw2_grupo_21\Model;

use DateTime;

final class User {
    private int $id;
    private string $name;
    private string $lastName;
    private string $email;
    private string $password;
    private string $birthdate;
    private string $phone;
    private float $balance;
    private string $iban;
    private DateTime $createdAt;
    private DateTime $updatedAt;
    

    //constructor
    public function __construct() {}

    public static function withParams(
        string $name,
        string $lastName,
        string $email,
        string $password,
        string $birthdate,
        string $phone,
        float $balance,
        DateTime $createdAt,
        DateTime $updatedAt

    ) {
        $instance = new self();
        $instance->name = $name;
        $instance->lastName = $lastName;
        $instance->email = $email;
        $instance->password = $password;
        $instance->birthdate = $birthdate;
        $instance->phone = $phone;
        $instance->balance = $balance;
        $instance->iban = "";
        $instance->createdAt = $createdAt;
        $instance->updatedAt = $updatedAt;
        return $instance;
    }

    //getters and setters
    public function id(): int {
        return $this->id;
    }

    public function setId(int $id): self {
        $this->id = $id;
        return $this;
    }

    public function name(): string {
        return $this->name;
    }

    public function lastName(): string {
        return $this->lastName;
    }

    public function email(): string {
        return $this->email;
    }

    public function password(): string {
        return $this->password;
    }

    public function birthdate(): string {
        return $this->birthdate;
    }

    public function phone(): string {
        return $this->phone;
    }

    public function createdAt(): DateTime {
        return $this->createdAt;
    }

    public function updatedAt(): DateTime {
        return $this->updatedAt;
    }

    public function balance() {
        return $this->balance;
    }

    public function IBAN(): string {
        return $this->iban;
    }
    
    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function setLastName(string $lastName): self {
        $this->lastName = $lastName;
        return $this;
    }

    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    public function setPhone(string $phone): self {
        $this->phone = $phone;
        return $this;
    }
    
    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }

    public function setBirthdate(string $birthdate): self {
        $this->birthdate = $birthdate;
        return $this;
    }

    public function setCreatedAt(DateTime $createdAt): void {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void {
        $this->updatedAt = $updatedAt;
    }

    public function setIBAN(string $iban): void {
        $this->iban = $iban;
    }

    public function setBalance(float $balance): void {
        $this->balance = $balance;
    }

}
