<?php

require_once __DIR__ . '/BaseModel.php';

class UserModel extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function findByEmailWithRole(string $email): ?array
    {
        $sql = 'SELECT u.*, r.name AS role_name, r.description AS role_description
                FROM users u
                INNER JOIN roles r ON u.role_id = r.id
                WHERE u.email = :email
                LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function createUser(array $data): int
    {
        $sql = 'INSERT INTO users (role_id, full_name, email, password, phone, avatar, status)
                VALUES (:role_id, :full_name, :email, :password, :phone, :avatar, :status)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'role_id'   => $data['role_id'] ?? 3,
            'full_name' => $data['full_name'],
            'email'     => $data['email'],
            'password'  => $data['password'],
            'phone'     => $data['phone'] ?? null,
            'avatar'    => $data['avatar'] ?? null,
            'status'    => $data['status'] ?? 'active',
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function getAllWithRole(): array
    {
        $sql = 'SELECT u.*, r.name AS role_name, r.description AS role_description
                FROM users u
                INNER JOIN roles r ON u.role_id = r.id
                ORDER BY u.id DESC';
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function getAllRoles(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM roles ORDER BY id ASC');

        return $stmt->fetchAll();
    }
}