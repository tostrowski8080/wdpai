<?php

require_once 'Repository.php';

class CategoryRepository extends Repository {
    public function addCategory(int $userId, string $name, string $color, string $icon): void {
        $stmt = $this->database->connect()->prepare('
            INSERT INTO categories (user_id, name, color_hex, icon_name)
            VALUES (:user_id, :name, :color, :icon)
        ');

        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':color', $color, PDO::PARAM_STR);
        $stmt->bindParam(':icon', $icon, PDO::PARAM_STR);

        $stmt->execute();
    }
    
    public function categoryExists(int $userId, string $name): bool {
        $stmt = $this->database->connect()->prepare('
            SELECT id FROM categories WHERE user_id = :user_id AND name = :name
        ');
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        
        return (bool)$stmt->fetch();
    }
}