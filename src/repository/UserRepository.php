<?php

require_once 'Repository.php';

class UserRepository extends Repository
{

    public function getUsers(): ?Users
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users
        ');
        $stmt->execute();

        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($users == false) {
            return null;
        }

        return $users;
    }

    public function getUserByEmail(string $email): ?array 
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users WHERE email = :email
        ');
        $stmt->bindParams('email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // TODO disconnect
        return $user;
    }

    public function createUser(string $email, string $hashedPassword, string $firstname, string $lastname, string $bio = '') 
    {
        $stmt = $this->database->connect()->prepare('
            INSERT INTO users (email, password, firstname, lastname, bio) VALUES (?, ?, ?, ?, ?)
        ')
        $stmt->execute($email, $hashedPassword, $firstname, $lastname, $bio);
    }
}