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

    public function getThisWeekActivitiesByUser(int $user_id)
    {
        $sunday = strtotime('last sunday', strtotime('tomorrow'));
        $start_date = date('Y-m-d 00:00:00', $sunday);

        $saturday = strtotime('next saturday', $sunday);
        $end_date = date('Y-m-d 23:59:59', $saturday);

        $stmt = $this->database->connect()->prepare('
            SELECT a.*, c.name as category_name, c.color_hex
            FROM activities a
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.user_id = :user_id
            AND (
                (a.start_time >= :start_date AND a.start_time <= :end_date)
                OR 
                (a.is_recurring = TRUE AND a.start_time <= :end_date)
            )
            ORDER BY a.start_time ASC
        ');

        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $start_date, PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $end_date, PDO::PARAM_STR);
    
        $stmt->execute();
        $rawActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->expandRecurringEvents($rawActivities, $start_date, $end_date);
    }

    public function getCategoriesByUserId(int $userId): array
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM categories 
            WHERE user_id = :user_id 
            ORDER BY name ASC
        ');
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActivityById(int $id): ?array
    {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM activities WHERE id = :id
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $activity = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($activity == false) {
            return null;
        }

        return $activity;
    }

    public function addActivity(array $data): void
    {
        $stmt = $this->database->connect()->prepare('
            INSERT INTO activities (
                user_id, category_id, title, start_time, end_time, 
                is_recurring, recurrence_pattern, is_completed
            )
            VALUES (
                :user_id, :category_id, :title, :start_time, :end_time, 
                :is_recurring, :recurrence_pattern, :is_completed
            )
        ');

        $userId = $data['user_id'];
        $categoryId = $data['category_id'];
        $title = $data['title'];
        $startTime = $data['start_time'];
        $endTime = $data['end_time'];
        $isRecurring = $data['is_recurring'] ? 1 : 0; 
        $recurrencePattern = $data['recurrence_pattern'];
        $isCompleted = 0;

        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':start_time', $startTime, PDO::PARAM_STR);
        $stmt->bindParam(':end_time', $endTime, PDO::PARAM_STR);
        $stmt->bindParam(':is_recurring', $isRecurring, PDO::PARAM_INT);
        $stmt->bindParam(':recurrence_pattern', $recurrencePattern, PDO::PARAM_STR);
        $stmt->bindParam(':is_completed', $isCompleted, PDO::PARAM_INT);

        $stmt->execute();
    }

    public function updateActivity(int $id, array $data): void
    {
        $stmt = $this->database->connect()->prepare('
            UPDATE activities 
            SET title = :title, 
                category_id = :category_id, 
                start_time = :start_time, 
                end_time = :end_time, 
                is_recurring = :is_recurring, 
                recurrence_pattern = :recurrence_pattern
            WHERE id = :id
        ');

        $title = $data['title'];
        $categoryId = $data['category_id'];
        $startTime = $data['start_time'];
        $endTime = $data['end_time'];
        $isRecurring = $data['is_recurring'] ? 1 : 0;
        $recurrencePattern = $data['recurrence_pattern'];

        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindParam(':start_time', $startTime, PDO::PARAM_STR);
        $stmt->bindParam(':end_time', $endTime, PDO::PARAM_STR);
        $stmt->bindParam(':is_recurring', $isRecurring, PDO::PARAM_INT);
        $stmt->bindParam(':recurrence_pattern', $recurrencePattern, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
    }

    private function expandRecurringEvents(array $activities, string $weekStart, string $weekEnd): array
    {
        $processedActivities = [];
        $weekStartTs = strtotime($weekStart);
        $weekEndTs = strtotime($weekEnd);

        foreach ($activities as $activity) {
            if (!$activity['is_recurring']) {
                $actStart = strtotime($activity['start_time']);
                if ($actStart >= $weekStartTs && $actStart <= $weekEndTs) {
                    $processedActivities[] = $activity;
                }
                continue;
            }

            //recurring logic
            $pattern = $activity['recurrence_pattern']; 
            $originalStart = strtotime($activity['start_time']);
            $originalEnd = strtotime($activity['end_time']);
            $duration = $originalEnd - $originalStart;

            $interval = 0;
            switch ($pattern) {
                case 'daily':     $interval = 86400; break;
                case 'weekly':    $interval = 604800; break;
                case 'bi-weekly': $interval = 1209600; break; 
                case 'monthly':   $interval = -1; break; 
            }

            if ($interval > 0) {

                if ($weekStartTs > $originalStart) {
                    $intervalsPassed = ceil(($weekStartTs - $originalStart) / $interval);
                    $currentInstance = $originalStart + ($intervalsPassed * $interval);
                } else {
                    $currentInstance = $originalStart;
                }
            } else {
                $currentInstance = $originalStart;
                while ($currentInstance < $weekStartTs) {
                    $currentInstance = strtotime('+1 month', $currentInstance);
                }
            }
            while ($currentInstance <= $weekEndTs) {
                if ($currentInstance >= $weekStartTs) {
                    $virtualActivity = $activity;
                    $virtualActivity['start_time'] = date('Y-m-d H:i:s', $currentInstance);
                    $virtualActivity['end_time'] = date('Y-m-d H:i:s', $currentInstance + $duration);
                    $processedActivities[] = $virtualActivity;
                }
            
                if ($interval > 0) {
                    $currentInstance += $interval;
                } else {
                    $currentInstance = strtotime('+1 month', $currentInstance);
                }
            }
        }

        usort($processedActivities, function ($a, $b) {
            return strtotime($a['start_time']) - strtotime($b['start_time']);
        });

        return $processedActivities;
    }

    public function checkAndCompleteExpiredActivities(int $userId): void
    {
        $stmt = $this->database->connect()->prepare('
            UPDATE activities 
            SET is_completed = TRUE 
            WHERE user_id = :user_id 
            AND is_completed = FALSE 
            AND is_recurring = FALSE
            AND end_time < CURRENT_TIMESTAMP
        ');
        
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getActivitiesForMonth(int $userId, int $month, int $year): array
    {
        $startDate = date("$year-$month-01 00:00:00");
        $endDate = date("Y-m-t 23:59:59", strtotime($startDate));

        $stmt = $this->database->connect()->prepare('
            SELECT a.*, c.name as category_name, c.color_hex
            FROM activities a
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.user_id = :user_id 
            AND (
                (a.start_time >= :start_date AND a.start_time <= :end_date)
                OR 
                (a.is_recurring = TRUE AND a.start_time <= :end_date)
            )
            ORDER BY a.start_time ASC
        ');

        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
        
        $stmt->execute();
        $rawActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->expandRecurringEvents($rawActivities, $startDate, $endDate);
    }

    public function getActivitiesByDateRange(int $userId, string $startDate, string $endDate): array
    {
        $stmt = $this->database->connect()->prepare('
            SELECT a.*, c.name as category_name, c.color_hex
            FROM activities a
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.user_id = :user_id 
            AND (
                (a.start_time >= :start_date AND a.start_time <= :end_date)
                OR 
                (a.is_recurring = TRUE AND a.start_time <= :end_date)
            )
            ORDER BY a.start_time ASC
        ');

        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
        
        $stmt->execute();
        $rawActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->expandRecurringEvents($rawActivities, $startDate, $endDate);
    }
}