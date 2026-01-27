<?php

require_once "AppController.php";
require_once __DIR__ . '/../repository/ActivityRepository.php';
require_once __DIR__ . '/../attribute/AllowedMethods.php';

class AddActivityController extends AppController {

    private $activityRepository;

    public function __construct() {
        $this->activityRepository = new ActivityRepository();
    }

    #[AllowedMethods(['POST', 'GET'])]
    public function add() {
        $this->checkSession();

        if ($this->isPost()) {
            if (empty($_POST['activity_name'])) {
                $categories = $this->activityRepository->getCategoriesByUserId($_SESSION['user_id']);
                return $this->render('add-activity', [
                    'messages' => 'Activity name is required.',
                    'categories' => $categories
                ]);
            }

            $useDeadline = isset($_POST['use_deadline']);
            $startTime = $useDeadline ? date('Y-m-d H:i:s') : ($_POST['date'] . ' ' . $_POST['start_time']);
            $endTime = $useDeadline ? ($_POST['deadline_date'] . ' 23:59:59') : ($_POST['date'] . ' ' . $_POST['end_time']);

            $data = [
                'user_id'           => $_SESSION['user_id'],
                'title'             => $_POST['activity_name'],
                'category_id'       => !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null,
                'is_recurring'      => isset($_POST['recurring']),
                'recurrence_pattern'=> isset($_POST['recurring']) ? $_POST['recurrence_pattern'] : null,
                'start_time'        => $startTime,
                'end_time'          => $endTime
            ];

            $this->activityRepository->addActivity($data);

            header("Location: /dashboard");
            exit();
        }

        $categories = $this->activityRepository->getCategoriesByUserId($_SESSION['user_id']);
        
        return $this->render('add-activity', ['categories' => $categories]);
    }

    #[AllowedMethods(['POST', 'GET'])]
    public function modify($id) {
        $this->checkSession();

        if (!$id) {
            $this->redirect('dashboard');
        }

        if ($this->isPost()) {
            $startTime = $_POST['date'] . ' ' . $_POST['start_time'];
            $endTime = $_POST['date'] . ' ' . $_POST['end_time'];

            $data = [
                'title'             => $_POST['activity_name'],
                'category_id'       => (int)$_POST['category_id'],
                'is_recurring'      => isset($_POST['recurring']),
                'recurrence_pattern'=> isset($_POST['recurring']) ? $_POST['recurrence_pattern'] : null,
                'start_time'        => $startTime,
                'end_time'          => $endTime
            ];

            $this->activityRepository->updateActivity($id, $data);
            $this->redirect('dashboard');
        }

        $activity = $this->activityRepository->getActivityById($id);
        $categories = $this->activityRepository->getCategoriesByUserId($_SESSION['user_id']);

        if (!$activity) {
            return $this->render('404');
        }

        return $this->render('add-activity', [
            'activity' => $activity, 
            'categories' => $categories, 
            'is_edit' => true
        ]);
    }

    private function checkSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('login');
        }
    }

    private function redirect($path) {
        header("Location: /$path");
        exit();
    }
}