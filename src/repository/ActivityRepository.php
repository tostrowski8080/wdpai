<?php

require_once 'Repository.php';

class ActivityRepository extends Repository {

    public function addActivity(array $data): void {
        $stmt = $this->database->connect()->prepare('
            INSERT INTO activities (user_id, category_id, title, is_recurring, recurrence_pattern, start_time, end_time)
            VALUES (:user_id, :category_id, :title, :is_recurring, :recurrence_pattern, :start_time, :end_time)
        ');

        $isRecurring = (bool)$data['is_recurring'];

        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $data['category_id'], PDO::PARAM_INT);
        $stmt->bindParam(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindParam(':is_recurring', $isRecurring, PDO::PARAM_BOOL);
        $stmt->bindParam(':recurrence_pattern', $data['recurrence_pattern'], PDO::PARAM_STR);
        $stmt->bindParam(':start_time', $data['start_time'], PDO::PARAM_STR);
        $stmt->bindParam(':end_time', $data['end_time'], PDO::PARAM_STR);

        $stmt->execute();
    }

    public function updateActivity(int $id, array $data): void {
        $stmt = $this->database->connect()->prepare('
            UPDATE activities 
            SET title = :title, category_id = :category_id, is_recurring = :is_recurring, 
                recurrence_pattern = :recurrence_pattern, start_time = :start_time, end_time = :end_time
            WHERE id = :id
        ');

        $isRecurring = (bool)$data['is_recurring'];
        
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':is_recurring', $isRecurring, PDO::PARAM_BOOL);
        $stmt->bindParam(':recurrence_pattern', $data['recurrence_pattern']);
        $stmt->bindParam(':start_time', $data['start_time']);
        $stmt->bindParam(':end_time', $data['end_time']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
    }

    public function getCategoriesByUserId(int $userId): array {
        $stmt = $this->database->connect()->prepare('
            SELECT * FROM categories WHERE user_id = :user_id
        ');
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getThisWeekActivitiesByUser(int $userId): array
    {
        $startOfWeek = date('Y-m-d 00:00:00', strtotime('last sunday', strtotime('tomorrow')));
        $endOfWeek = date('Y-m-d 23:59:59', strtotime('+6 days', strtotime($startOfWeek)));

        $stmt = $this->database->connect()->prepare('
            SELECT a.*, c.name as category_name, c.color_hex, c.icon_name
            FROM activities a
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.user_id = :user_id 
            AND (
                (a.start_time >= :start_date AND a.start_time <= :end_date)
                OR 
                (a.is_recurring = TRUE) 
            )
            ORDER BY a.start_time ASC
        ');

        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $startOfWeek, PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $endOfWeek, PDO::PARAM_STR);
        
        $stmt->execute();
        $rawActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->expandRecurringEvents($rawActivities, $startOfWeek, $endOfWeek);
    }

    public function getActivitiesByDateRange(int $userId, string $startDate, string $endDate): array
    {
        $stmt = $this->database->connect()->prepare('
            SELECT a.*, c.name as category_name, c.color_hex, c.icon_name
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

    public function getActivityById(int $id): ?array
    {
        $stmt = $this->database->connect()->prepare('
            SELECT a.*, c.name as category_name, c.color_hex, c.icon_name
            FROM activities a
            LEFT JOIN categories c ON a.category_id = c.id
            WHERE a.id = :id
        ');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $activity = $stmt->fetch(PDO::FETCH_ASSOC);
        return $activity ?: null;
    }

    public function checkAndCompleteExpiredActivities(int $userId): void {
        $now = date('Y-m-d H:i:s');
        
        $stmt = $this->database->connect()->prepare('
            UPDATE activities 
            SET is_completed = TRUE 
            WHERE user_id = :user_id 
            AND is_recurring = FALSE 
            AND end_time < :now 
            AND is_completed = FALSE
        ');
        
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':now', $now, PDO::PARAM_STR);
        $stmt->execute();
    }

    private function expandRecurringEvents(array $activities, string $rangeStart, string $rangeEnd): array {
        $expanded = [];
        $rangeStartTs = strtotime($rangeStart);
        $rangeEndTs = strtotime($rangeEnd);

        foreach ($activities as $activity) {
            if (!$activity['is_recurring']) {
                $actStart = strtotime($activity['start_time']);
                if ($actStart >= $rangeStartTs && $actStart <= $rangeEndTs) {
                    $expanded[] = $activity;
                }
                continue;
            }

            $actStartTs = strtotime($activity['start_time']);
            $actEndTs = strtotime($activity['end_time']);
            $duration = $actEndTs - $actStartTs;
            $pattern = $activity['recurrence_pattern'];

            $diffDays = floor(($rangeEndTs - $rangeStartTs) / (60 * 60 * 24));

            for ($i = 0; $i <= $diffDays; $i++) {
                $currentDayTs = strtotime("+$i days", $rangeStartTs);
                
                if ($currentDayTs < strtotime(date('Y-m-d 00:00:00', $actStartTs))) {
                    continue;
                }

                $shouldAdd = false;

                if ($pattern === 'daily') {
                    $shouldAdd = true;
                } 
                elseif ($pattern === 'weekly') {
                    if (date('w', $currentDayTs) == date('w', $actStartTs)) {
                        $shouldAdd = true;
                    }
                }
                elseif ($pattern === 'bi-weekly') {
                    if (date('w', $currentDayTs) == date('w', $actStartTs)) {
                        $weekDiff = floor(($currentDayTs - $actStartTs) / (60 * 60 * 24 * 7));
                        if ($weekDiff % 2 == 0) {
                            $shouldAdd = true;
                        }
                    }
                }
                elseif ($pattern === 'monthly') {
                    if (date('j', $currentDayTs) == date('j', $actStartTs)) {
                        $shouldAdd = true;
                    }
                }

                if ($shouldAdd) {
                    $instance = $activity;
                    
                    $newStart = date('Y-m-d', $currentDayTs) . ' ' . date('H:i:s', $actStartTs);
                    $newEnd = date('Y-m-d H:i:s', strtotime($newStart) + $duration);

                    $instance['start_time'] = $newStart;
                    $instance['end_time'] = $newEnd;

                    $expanded[] = $instance;
                }
            }
        }
        
        usort($expanded, function($a, $b) {
            return strtotime($a['start_time']) - strtotime($b['start_time']);
        });

        return $expanded;
    }
}