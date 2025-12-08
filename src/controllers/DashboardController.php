<?php

require_once "AppController.php";
require_once __DIR__.'/../repository/UserRepository.php';
require_once __DIR__.'/../repository/CardsRepository.php';

class DashboardController extends AppController {

    // TODO zmiana na dashboard
    private $cardsRepository;

    public function __construct(){
        $this->cardsRepository = new CardsRepository();
    }

    public function index(){
        $usersRepository = new UserRepository();
        $users = $usersRepository->getUsers();

        var_dump($users);

        return $this->render("dashboard", ['items' => $cards]);
    }

    public function search(){
        header('Content-Type: application/json');

        if (!$this->isPost()){
            http_response_code(405);
            echo json_encode([
                'status' => 'Method not allowed'
            ]);
            return;
        }

        $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        if ($contentType !== "application/json") {
            http_response_code(415);
            echo_json_encode([
                'status' => 'Application/json content type not found'
            ]);
            return;
        }

        $content = trim(file_get_contents("php://input"));
        $decoded = json_decode($content, true);
        $searchTag = $decoded['search'];

        http_response_code(200);
        echo json_encode([
            'status' => 'ok',
            'cards' => $this->cardsRepository->getCardsByTitle($searchTag)
        ]);

        return;
    }
}