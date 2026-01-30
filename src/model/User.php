<?php

class User {
    private $email;
    private $password;
    private $name;
    private $surname;
    private $id;
    private $role;

    public function __construct(
        string $email,
        string $password,
        string $name,
        string $surname,
        int $id = null, 
        string $role = 'standard'

    ) {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
        $this->id = $id;
        $this->role = $role;
    }

    public function getEmail(): string 
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): void
    {
        $this->surname = $surname;
    }
    public function getRole(): string {
        return $this->role;
    }
    public function getId(): ?int {
        return $this->id;
    }
}