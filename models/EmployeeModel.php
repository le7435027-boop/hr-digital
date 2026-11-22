<?php

require_once __DIR__ . '/BaseModel.php';

class EmployeeModel extends BaseModel
{
    public function getAllWithDepartment(): array
    {
        $sql = 'SELECT e.*, d.department_name
                FROM employees e
                LEFT JOIN departments d ON e.department_id = d.id
                ORDER BY e.id DESC';
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $sql = 'SELECT e.*, d.department_name
                FROM employees e
                LEFT JOIN departments d ON e.department_id = d.id
                WHERE e.id = :id
                LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function findByUserId(int $userId): ?array
    {
        $sql = 'SELECT e.*, d.department_name
                FROM employees e
                LEFT JOIN departments d ON e.department_id = d.id
                WHERE e.user_id = :user_id
                LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function createEmployee(array $data): int
    {
        $sql = 'INSERT INTO employees (
                    user_id, department_id, employee_code, full_name, gender,
                    date_of_birth, email, phone, address, position, hire_date, status
                ) VALUES (
                    :user_id, :department_id, :employee_code, :full_name, :gender,
                    :date_of_birth, :email, :phone, :address, :position, :hire_date, :status
                )';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id'       => $data['user_id'] ?? null,
            'department_id' => $data['department_id'] ?? null,
            'employee_code' => $data['employee_code'],
            'full_name'     => $data['full_name'],
            'gender'        => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?: null,
            'email'         => $data['email'] ?: null,
            'phone'         => $data['phone'] ?: null,
            'address'       => $data['address'] ?: null,
            'position'      => $data['position'] ?: null,
            'hire_date'     => $data['hire_date'] ?: null,
            'status'        => $data['status'] ?? 'Đang làm',
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateEmployee(int $id, array $data): bool
    {
        $sql = 'UPDATE employees
                SET department_id = :department_id,
                    employee_code = :employee_code,
                    full_name = :full_name,
                    gender = :gender,
                    date_of_birth = :date_of_birth,
                    email = :email,
                    phone = :phone,
                    address = :address,
                    position = :position,
                    hire_date = :hire_date,
                    status = :status
                WHERE id = :id';
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id'            => $id,
            'department_id' => $data['department_id'] ?? null,
            'employee_code' => $data['employee_code'],
            'full_name'     => $data['full_name'],
            'gender'        => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?: null,
            'email'         => $data['email'] ?: null,
            'phone'         => $data['phone'] ?: null,
            'address'       => $data['address'] ?: null,
            'position'      => $data['position'] ?: null,
            'hire_date'     => $data['hire_date'] ?: null,
            'status'        => $data['status'] ?? 'Đang làm',
        ]);
    }

    public function deleteById(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM employees WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }

    public function getAllDepartments(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM departments ORDER BY id ASC');

        return $stmt->fetchAll();
    }
}