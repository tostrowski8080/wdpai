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

        $view = $_GET['view'] ?? 'month';

        $refDate = $_GET['date'] ?? date('Y-m-d');
        $ts = strtotime($refDate);

        $startDate = '';
        $endDate = '';
        $title = '';
        $prevDate = '';
        $nextDate = '';

        switch ($view) {
            case 'day':
                $title = date('F j, Y', $ts);
                
                $startDate = date('Y-m-d 00:00:00', $ts);
                $endDate = date('Y-m-d 23:59:59', $ts);

                $prevDate = date('Y-m-d', strtotime('-1 day', $ts));
                $nextDate = date('Y-m-d', strtotime('+1 day', $ts));
                break;

            case 'week':
                $startOfWeek = (date('w', $ts) == 0) ? $ts : strtotime('last sunday', $ts);
                $endOfWeek = strtotime('+6 days', $startOfWeek);

                $title = date('M j', $startOfWeek) . ' - ' . date('M j, Y', $endOfWeek);

                $startDate = date('Y-m-d 00:00:00', $startOfWeek);
                $endDate = date('Y-m-d 23:59:59', $endOfWeek);

                $prevDate = date('Y-m-d', strtotime('-1 week', $ts));
                $nextDate = date('Y-m-d', strtotime('+1 week', $ts));
                break;

            case 'month':
            default:
                $title = date('F Y', $ts);

                $startDate = date('Y-m-01 00:00:00', $ts);
                $endDate = date('Y-m-t 23:59:59', $ts);

                $prevDate = date('Y-m-d', strtotime('-1 month', $ts));
                $nextDate = date('Y-m-d', strtotime('+1 month', $ts));
                break;
        }

        $activities = $this->activityRepository->getActivitiesByDateRange($_SESSION['user_id'], $startDate, $endDate);

        $activitiesByDay = [];
        foreach ($activities as $act) {
            $dayKey = date('Y-m-d', strtotime($act['start_time']));
            $activitiesByDay[$dayKey][] = $act;
        }

        $daysInMonth = date('t', $ts);
        $firstDayOfMonth = strtotime(date('Y-m-01', $ts));
        $paddingLeft = date('w', $firstDayOfMonth);

        return $this->render('calendar', [
            'view' => $view,
            'currentDate' => $refDate,
            'title' => $title,
            'prevLink' => "?view=$view&date=$prevDate",
            'nextLink' => "?view=$view&date=$nextDate",
            
            'activities' => $activities,
            'activitiesByDay' => $activitiesByDay,
            
            'daysInMonth' => $daysInMonth,
            'paddingLeft' => $paddingLeft,
            'year' => date('Y', $ts),
            'month' => date('m', $ts),
            
            'weekStartTs' => (isset($startOfWeek) ? $startOfWeek : 0)
        ]);
    }

    private function checkSession() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: /login");
            exit();
        }
    }
}