<?php
$page   = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$publicPages = ['login', 'register'];

if (!in_array($page, $publicPages, true) && empty($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

switch ($page) {
    case 'login':
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->authenticate();
        } else {
            $controller->login();
        }
        break;

    case 'register':
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->store();
        } else {
            $controller->create();
        }
        break;

    case 'logout':
        require_once __DIR__ . '/../controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'dashboard':
        require_once __DIR__ . '/../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;

    case 'employees':
        require_once __DIR__ . '/../controllers/EmployeeController.php';
        $controller = new EmployeeController();
        if ($action === 'create') {
            $controller->create();
        } elseif ($action === 'store') {
            $controller->store();
        } elseif ($action === 'edit') {
            $controller->edit();
        } elseif ($action === 'update') {
            $controller->update();
        } elseif ($action === 'delete') {
            $controller->delete();
        } elseif ($action === 'profile') {
            $controller->profile();
        } else {
            $controller->index();
        }
        break;

    case 'documents':
        require_once __DIR__ . '/../controllers/DocumentController.php';
        $controller = new DocumentController();
        if ($action === 'upload') {
            $controller->upload();
        } elseif ($action === 'store') {
            $controller->store();
        } elseif ($action === 'review') {
            $controller->review();
        } elseif ($action === 'store_review') {
            $controller->storeReview();
        } else {
            $controller->index();
        }
        break;

    case 'admin':
        require_once __DIR__ . '/../controllers/AdminController.php';
        $controller = new AdminController();
        if ($action === 'categories') {
            $controller->categories();
        } else {
            $controller->users();
        }
        break;

    default:
        require_once __DIR__ . '/../controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
}