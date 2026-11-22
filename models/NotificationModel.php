<?php

require_once __DIR__ . '/BaseModel.php';

class NotificationModel extends BaseModel
{
    public function getByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC, id DESC'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll();
    }

    public function countUnreadByUserId(int $userId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0'
        );
        $stmt->execute(['user_id' => $userId]);

        return (int)$stmt->fetchColumn();
    }

    public function createNotification(array $data): int
    {
        $sql = 'INSERT INTO notifications (user_id, title, message, is_read)
                VALUES (:user_id, :title, :message, :is_read)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $data['user_id'],
            'title'   => $data['title'],
            'message' => $data['message'] ?? null,
            'is_read' => $data['is_read'] ?? 0,
        ]);

        return (int)$this->pdo->lastInsertId();
    }
}