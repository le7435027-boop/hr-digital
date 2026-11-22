<?php

require_once __DIR__ . '/BaseModel.php';

class DocumentModel extends BaseModel
{
    public function getAllWithDetails(): array
    {
        $sql = 'SELECT d.*,
                       dt.name AS document_type_name,
                       e.employee_code,
                       e.full_name AS employee_name,
                       e.user_id AS owner_user_id,
                       dep.department_name
                FROM documents d
                INNER JOIN document_types dt ON d.document_type_id = dt.id
                INNER JOIN employees e ON d.employee_id = e.id
                LEFT JOIN departments dep ON e.department_id = dep.id
                ORDER BY d.id DESC';
        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function getByUserId(int $userId): array
    {
        $sql = 'SELECT d.*,
                       dt.name AS document_type_name,
                       e.employee_code,
                       e.full_name AS employee_name,
                       e.user_id AS owner_user_id
                FROM documents d
                INNER JOIN document_types dt ON d.document_type_id = dt.id
                INNER JOIN employees e ON d.employee_id = e.id
                WHERE e.user_id = :user_id
                ORDER BY d.id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function findByIdWithDetails(int $id): ?array
    {
        $sql = 'SELECT d.*,
                       dt.name AS document_type_name,
                       e.employee_code,
                       e.full_name AS employee_name,
                       e.user_id AS owner_user_id,
                       dep.department_name
                FROM documents d
                INNER JOIN document_types dt ON d.document_type_id = dt.id
                INNER JOIN employees e ON d.employee_id = e.id
                LEFT JOIN departments dep ON e.department_id = dep.id
                WHERE d.id = :id
                LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function createDocument(array $data): int
    {
        $sql = 'INSERT INTO documents (
                    employee_id, document_type_id, document_name, file_path, description, status
                ) VALUES (
                    :employee_id, :document_type_id, :document_name, :file_path, :description, :status
                )';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'employee_id'      => $data['employee_id'],
            'document_type_id' => $data['document_type_id'],
            'document_name'    => $data['document_name'],
            'file_path'        => $data['file_path'],
            'description'      => $data['description'] ?? null,
            'status'           => $data['status'] ?? 'Pending',
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->pdo->prepare('UPDATE documents SET status = :status WHERE id = :id');

        return $stmt->execute([
            'id'     => $id,
            'status' => $status,
        ]);
    }

    public function getReviewsByDocumentId(int $documentId): array
    {
        $sql = 'SELECT dr.*, u.full_name AS reviewer_name
                FROM document_reviews dr
                INNER JOIN users u ON dr.reviewer_id = u.id
                WHERE dr.document_id = :document_id
                ORDER BY dr.reviewed_at DESC, dr.id DESC';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['document_id' => $documentId]);

        return $stmt->fetchAll();
    }

    public function addReview(array $data): int
    {
        $sql = 'INSERT INTO document_reviews (document_id, reviewer_id, action, comment)
                VALUES (:document_id, :reviewer_id, :action, :comment)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'document_id' => $data['document_id'],
            'reviewer_id' => $data['reviewer_id'],
            'action'      => $data['action'],
            'comment'     => $data['comment'] ?? null,
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function getAllDocumentTypes(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM document_types ORDER BY id ASC');

        return $stmt->fetchAll();
    }
}