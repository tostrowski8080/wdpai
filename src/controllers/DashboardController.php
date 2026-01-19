<?php

require_once "AppController.php";
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/ActivityRepository.php';

class DashboardController extends AppController {
    
    private $activityRepository;
    
    public function __construct() {
        $this->activityRepository = new ActivityRepository();
    }

    public function index(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            $url = "http://$_SERVER[HTTP_HOST]";
            header("Location: {$url}/login");
            exit();
        }

        $userId = $_SESSION['user_id'];
        
        $activities = $this->activityRepository->getThisWeekActivitiesByUser($userId);

        $todayActivities = [];
        $upcomingActivities = [];
        $weekGrid = [
            'Sunday' => [], 'Monday' => [], 'Tuesday' => [], 'Wednesday' => [],
            'Thursday' => [], 'Friday' => [], 'Saturday' => []
        ];
        
        $stats = [
            'hours_this_week' => 0,
            'completed_count' => 0,
            'total_count' => 0
        ];

        $currentDate = date('Y-m-d');

        foreach ($activities as $activity) {
            $start = strtotime($activity['start_time']);
            $end = strtotime($activity['end_time']);
            $dateString = date('Y-m-d', $start);
            $dayName = date('l', $start);

            if ($dateString === $currentDate) {
                $todayActivities[] = $activity;
                $stats['total_count']++;
                if ($activity['is_completed']) {
                    $stats['completed_count']++;
                }
            } 
            elseif ($dateString > $currentDate) {
                if (count($upcomingActivities) < 8) {
                    $upcomingActivities[] = $activity;
                }
            }

            if (isset($weekGrid[$dayName])) {
                $weekGrid[$dayName][] = $activity;
            }

            $durationHours = ($end - $start) / 3600;
            $stats['hours_this_week'] += $durationHours;
        }

        return $this->render('dashboard', [
            'user_name' => $_SESSION['user_name'] ?? 'User',
            'today_activities' => $todayActivities,
            'upcoming_activities' => $upcomingActivities, 
            'week_grid' => $weekGrid,
            'stats' => $stats,
        ]);
    }
}