<?php

require_once 'Repository.php';

class UserRepository extends Repository
{
    private static $instance;

    public static function getInstance() {
        return self::$instance ??= new UserRepository();
    }

    public function getUsers(): ?array
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

    public function getUserByEmail(string $email)
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users WHERE email = :email
        ');
        
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user == false) {
            return null;
        }
        
        return $user;
    }

    public function createUser(string $email, string $hashedPassword, string $firstname, string $lastname) 
    {
        $pdo = $this->database->connect();
        
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('
                INSERT INTO users (email, password, firstname, lastname) 
                VALUES (:email, :password, :firstname, :lastname)
                RETURNING id
            ');

            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);

            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $userId = $result['id'];

            $defaultCategories = [
                ['Work',      '#2196f3', ''],
                ['Health',    '#00bcd4', ''],
                ['Personal',  '#ff9800', ''],
                ['Education', '#9c27b0', '']
            ];

            $catStmt = $pdo->prepare('
                INSERT INTO categories (user_id, name, color_hex, icon_name) 
                VALUES (?, ?, ?, ?)
            ');

            foreach ($defaultCategories as $cat) {
                $catStmt->execute([
                    $userId,
                    $cat[0],
                    $cat[1],
                    $cat[2]
                ]);
            }

            $pdo->commit();

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    public function getUserById(int $id): ?array {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM users WHERE id = :id
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    public function updateUserInfo(int $id, string $firstname, string $lastname, string $email): void {
        $stmt = $this->database->connect()->prepare('
            UPDATE users 
            SET firstname = :firstname, lastname = :lastname, email = :email 
            WHERE id = :id
        ');
        $stmt->bindParam(':firstname', $firstname, PDO::PARAM_STR);
        $stmt->bindParam(':lastname', $lastname, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function updateUserPassword(int $id, string $hashedPassword): void {
        $stmt = $this->database->connect()->prepare('
            UPDATE users SET password = :password WHERE id = :id
        ');
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function deleteUser(int $id): void {
        $pdo = $this->database->connect();
        
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('DELETE FROM activities WHERE user_id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $pdo->prepare('DELETE FROM categories WHERE user_id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}