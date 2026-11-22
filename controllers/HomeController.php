<?php

class HomeController
{
    public function index(): void
    {
        require_once PATH_MODEL . 'NotificationModel.php';
        $notificationModel = new NotificationModel();

        $userId              = (int)($_SESSION['user_id'] ?? 0);
        $roleName            = $_SESSION['role_name'] ?? 'user';
        $fullName            = $_SESSION['full_name'] ?? 'Người dùng';
        $notifications       = $notificationModel->getByUserId($userId);
        $unreadNotifications = $notificationModel->countUnreadByUserId($userId);
        $contentView         = PATH_VIEW . 'dashboard/index.php';

        require_once PATH_VIEW_MAIN;
    }
}