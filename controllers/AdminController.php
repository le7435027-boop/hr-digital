<?php
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/EmployeeModel.php';
require_once __DIR__ . '/../models/DocumentModel.php';

class AdminController
{
    private function ensureAdmin(): void
    {
        if (($_SESSION['role_name'] ?? '') !== 'admin') {
            header('Location: index.php?page=dashboard');
            exit;
        }
    }

    public function users(): void
    {
        $this->ensureAdmin();

        require_once PATH_MODEL . 'UserModel.php';
        $userModel   = new UserModel();
        $users       = $userModel->getAllWithRole();
        $roles       = $userModel->getAllRoles();
        $contentView = PATH_VIEW . 'admin/users.php';

        require_once PATH_VIEW_MAIN;
    }

    public function categories(): void
    {
        $this->ensureAdmin();

        require_once PATH_MODEL . 'EmployeeModel.php';
        require_once PATH_MODEL . 'DocumentModel.php';

        $employeeModel = new EmployeeModel();
        $documentModel = new DocumentModel();

        $departments   = $employeeModel->getAllDepartments();
        $documentTypes = $documentModel->getAllDocumentTypes();
        $contentView   = PATH_VIEW . 'admin/categories.php';

        require_once PATH_VIEW_MAIN;
    }
}