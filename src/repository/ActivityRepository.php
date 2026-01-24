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
            SELECT a.*, c.name as category_name 
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
}