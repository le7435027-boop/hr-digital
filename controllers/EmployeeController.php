<?php

class EmployeeController
{
    private function ensureRole(array $allowedRoles): void
    {
        $role = $_SESSION['role_name'] ?? '';
        if (!in_array($role, $allowedRoles, true)) {
            header('Location: index.php?page=dashboard');
            exit;
        }
    }

    public function index(): void
    {
        $this->ensureRole(['admin', 'hr']);

        require_once PATH_MODEL . 'EmployeeModel.php';
        $employeeModel = new EmployeeModel();
        $employees     = $employeeModel->getAllWithDepartment();
        $contentView   = PATH_VIEW . 'employees/index.php';

        require_once PATH_VIEW_MAIN;
    }

    public function create(): void
    {
        $this->ensureRole(['admin', 'hr']);

        $employee    = null;
        $contentView = PATH_VIEW . 'employees/form.php';

        require_once PATH_VIEW_MAIN;
    }

    public function store(): void
    {
        $this->ensureRole(['admin', 'hr']);

        require_once PATH_MODEL . 'EmployeeModel.php';
        $employeeModel = new EmployeeModel();
        $employeeModel->createEmployee([
            'user_id'       => !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null,
            'department_id' => !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null,
            'employee_code' => trim($_POST['employee_code'] ?? ''),
            'full_name'     => trim($_POST['full_name'] ?? ''),
            'gender'        => $_POST['gender'] ?? null,
            'date_of_birth' => $_POST['date_of_birth'] ?? null,
            'email'         => trim($_POST['email'] ?? ''),
            'phone'         => trim($_POST['phone'] ?? ''),
            'address'       => trim($_POST['address'] ?? ''),
            'position'      => trim($_POST['position'] ?? ''),
            'hire_date'     => $_POST['hire_date'] ?? null,
            'status'        => $_POST['status'] ?? 'Đang làm',
        ]);

        header('Location: index.php?page=employees');
        exit;
    }

    public function edit(): void
    {
        $this->ensureRole(['admin', 'hr']);

        $id = (int)($_GET['id'] ?? 0);
        require_once PATH_MODEL . 'EmployeeModel.php';
        $employeeModel = new EmployeeModel();
        $employee      = $employeeModel->findById($id);
        $contentView   = PATH_VIEW . 'employees/form.php';

        require_once PATH_VIEW_MAIN;
    }

    public function update(): void
    {
        $this->ensureRole(['admin', 'hr']);

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        require_once PATH_MODEL . 'EmployeeModel.php';
        $employeeModel = new EmployeeModel();
        $employeeModel->updateEmployee($id, [
            'department_id' => !empty($_POST['department_id']) ? (int)$_POST['department_id'] : null,
            'employee_code' => trim($_POST['employee_code'] ?? ''),
            'full_name'     => trim($_POST['full_name'] ?? ''),
            'gender'        => $_POST['gender'] ?? null,
            'date_of_birth' => $_POST['date_of_birth'] ?? null,
            'email'         => trim($_POST['email'] ?? ''),
            'phone'         => trim($_POST['phone'] ?? ''),
            'address'       => trim($_POST['address'] ?? ''),
            'position'      => trim($_POST['position'] ?? ''),
            'hire_date'     => $_POST['hire_date'] ?? null,
            'status'        => $_POST['status'] ?? 'Đang làm',
        ]);

        header('Location: index.php?page=employees');
        exit;
    }

    public function delete(): void
    {
        $this->ensureRole(['admin', 'hr']);

        $id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
        require_once PATH_MODEL . 'EmployeeModel.php';
        $employeeModel = new EmployeeModel();
        $employeeModel->deleteById($id);

        header('Location: index.php?page=employees');
        exit;
    }

    public function profile(): void
    {
        $userId = (int)($_SESSION['user_id'] ?? 0);
        require_once PATH_MODEL . 'EmployeeModel.php';
        $employeeModel = new EmployeeModel();
        $employee      = $employeeModel->findByUserId($userId);
        $contentView   = PATH_VIEW . 'employees/profile.php';

        require_once PATH_VIEW_MAIN;
    }
}