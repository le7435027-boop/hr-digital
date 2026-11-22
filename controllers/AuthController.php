<?php

class AuthController
{
    private function renderAuthView(string $viewFile, array$data = []): void
    {
        extract($data, EXTR_SKIP);
        $filePath = PATH_VIEW . 'auth/' .$viewFile . '.php';
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }

    public function login(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        $this->renderAuthView('login', [
            'error' => $_SESSION['auth_error'] ?? null,
        ]);
        unset($_SESSION['auth_error']);
    }

    public function authenticate(): void
    {
        $email    = trim($_POST['email'] ?? '');
        $password =$_POST['password'] ?? '';

        if ($email === '' || $password === '') {$_SESSION['auth_error'] = 'Vui lòng nhập đầy đủ email và mật khẩu.';
            header('Location: index.php?page=login');
            exit;
        }

        require_once PATH_MODEL . 'UserModel.php';
        $userModel = new UserModel();$user = $userModel->findByEmailWithRole($email);

        if (!$user || !password_verify($password, $user['password'])) {$_SESSION['auth_error'] = 'Email hoặc mật khẩu không chính xác.';
            header('Location: index.php?page=login');
            exit;
        }

        if (($user['status'] ?? 'active') !== 'active') {$_SESSION['auth_error'] = 'Tài khoản đang bị khóa hoặc ngừng hoạt động.';
            header('Location: index.php?page=login');
            exit;
        }

        session_regenerate_id(true);
        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['role_id']   = (int)$user['role_id'];
        $_SESSION['role_name'] =$user['role_name'] ?? 'user';
        $_SESSION['full_name'] =$user['full_name'];
        $_SESSION['email']     =$user['email'];

        header('Location: index.php?page=dashboard');
        exit;
    }

    public function create(): void
    {
        $this->renderAuthView('create', [
            'error' => $_SESSION['register_error'] ?? null,
        ]);
        unset($_SESSION['register_error']);
    }

    public function store(): void
    {
        $fullName        = trim($_POST['full_name'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $phone           = trim($_POST['phone'] ?? '');
        $password        =$_POST['password'] ?? '';
        $confirmPassword =$_POST['confirm_password'] ?? '';

        if ($fullName === ''||$email === '' || $password === '') {$_SESSION['register_error'] = 'Vui lòng nhập họ tên, email và mật khẩu.';
            header('Location: index.php?page=register');
            exit;
        }

        if ($password !== $confirmPassword) {$_SESSION['register_error'] = 'Mật khẩu xác nhận không khớp.';
            header('Location: index.php?page=register');
            exit;
        }

        require_once PATH_MODEL . 'UserModel.php';
        $userModel = new UserModel();

        if ($userModel->findByEmail($email)) {$_SESSION['register_error'] = 'Email này đã được sử dụng.';
            header('Location: index.php?page=register');
            exit;
        }

        $userModel->createUser([
            'role_id'   => 3, // Mặc định role 'user' (Nhân viên công ty)
            'full_name' => $fullName,
            'email'     => $email,
            'phone'     => $phone !== '' ?$phone : null,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
            'status'    => 'active',
        ]);

        header('Location: index.php?page=login');
        exit;
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        header('Location: index.php?page=login');
        exit;
    }
}