<?php

require_once "AppController.php";
require_once __DIR__ . '/../repository/ActivityRepository.php';

class CalendarController extends AppController {

    private $activityRepository;

    public function __construct() {
        $this->activityRepository = new ActivityRepository();
    }

    public function index() {
        $this->checkSession();

        // 1. Get View Mode (month, week, day) - Default to 'month'
        $view = $_GET['view'] ?? 'month';

        // 2. Get Reference Date - Default to Today
        $refDate = $_GET['date'] ?? date('Y-m-d');
        $ts = strtotime($refDate); // Timestamp

        $startDate = '';
        $endDate = '';
        $title = '';
        $prevDate = '';
        $nextDate = '';

        // 3. Logic Switch based on View
        switch ($view) {
            case 'day':
                // Title: "January 25, 2026"
                $title = date('F j, Y', $ts);
                
                // Range: The specific day (00:00 to 23:59)
                $startDate = date('Y-m-d 00:00:00', $ts);
                $endDate = date('Y-m-d 23:59:59', $ts);

                // Navigation: +/- 1 day
                $prevDate = date('Y-m-d', strtotime('-1 day', $ts));
                $nextDate = date('Y-m-d', strtotime('+1 day', $ts));
                break;

            case 'week':
                // Calculate Start of Week (Sunday) and End (Saturday)
                // Note: 'last sunday' logic handles if today is Sunday correctly
                $startOfWeek = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
                $endOfWeek = strtotime('+6 days', $startOfWeek);

                // Title: "Jan 25 - Jan 31, 2026"
                $title = date('M j', $startOfWeek) . ' - ' . date('M j, Y', $endOfWeek);

                $startDate = date('Y-m-d 00:00:00', $startOfWeek);
                $endDate = date('Y-m-d 23:59:59', $endOfWeek);

                // Navigation: +/- 1 week
                $prevDate = date('Y-m-d', strtotime('-1 week', $ts));
                $nextDate = date('Y-m-d', strtotime('+1 week', $ts));
                break;

            case 'month':
            default:
                // Title: "January 2026"
                $title = date('F Y', $ts);

                // Range: First to Last day of month
                $startDate = date('Y-m-01 00:00:00', $ts);
                $endDate = date('Y-m-t 23:59:59', $ts);

                // Navigation: +/- 1 month
                $prevDate = date('Y-m-d', strtotime('-1 month', $ts));
                $nextDate = date('Y-m-d', strtotime('+1 month', $ts));
                break;
        }

        // 4. Fetch Data
        $activities = $this->activityRepository->getActivitiesByDateRange($_SESSION['user_id'], $startDate, $endDate);

        // 5. Organize Data (Group by Day Number for Month/Week views)
        $activitiesByDay = [];
        foreach ($activities as $act) {
            // Key format: "2026-01-25" (Full date string is safer for week view spanning months)
            $dayKey = date('Y-m-d', strtotime($act['start_time']));
            $activitiesByDay[$dayKey][] = $act;
        }

        // 6. Additional Data for Month Grid
        $daysInMonth = date('t', $ts);
        $firstDayOfMonth = strtotime(date('Y-m-01', $ts));
        $paddingLeft = date('w', $firstDayOfMonth);

        return $this->render('calendar', [
            'view' => $view,
            'currentDate' => $refDate,
            'title' => $title,
            'prevLink' => "?view=$view&date=$prevDate",
            'nextLink' => "?view=$view&date=$nextDate",
            
            // Data
            'activities' => $activities,         // Flat list (good for Day view)
            'activitiesByDay' => $activitiesByDay, // Grouped list (good for Grid views)
            
            // Month Grid Helpers
            'daysInMonth' => $daysInMonth,
            'paddingLeft' => $paddingLeft,
            'year' => date('Y', $ts),
            'month' => date('m', $ts),
            
            // Week Helpers
            'weekStartTs' => (isset($startOfWeek) ? $startOfWeek : 0)
        ]);
    }

    private function checkSession() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: http://$_SERVER[HTTP_HOST]/login");
            exit();
        }
    }
}