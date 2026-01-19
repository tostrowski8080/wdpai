<?php

require_once 'Repository.php';

class ActivityRepository extends Repository {
    private static $instance;

    public static function getInstance() {
        return self::$instance ??= new UserRepository();
    }

    public function getActivities(): ?array
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM activities
        ');
        $stmt->execute();

        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($activities == false) {
            return null;
        }

        return $activities;
    }

    public function getActivitiesByUser(int $user_id)
    {
        $stmt = $this->database->connect()->prepare('
            SELECT a.*, c.name as category_name 
            FROM activities a
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.user_id = :user_id
            ORDER BY a.start_time ASC
        ');
    
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $activities;
    }

        public function getThisWeekActivitiesByUser(int $user_id)
    {
        $stmt = $this->database->connect()->prepare('
            SELECT a.*, c.name as category_name 
            FROM activities a
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.user_id = :user_id
            ORDER BY a.start_time ASC
        ');
    
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    
        $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $activities;
    }
}