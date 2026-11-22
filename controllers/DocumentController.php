<?php

class DocumentController
{
    public function index(): void
    {
        $roleName = $_SESSION['role_name'] ?? 'user';
        $userId   = (int)($_SESSION['user_id'] ?? 0);

        require_once PATH_MODEL . 'DocumentModel.php';
        $documentModel = new DocumentModel();

        if (in_array($roleName, ['admin', 'hr'], true)) {
            $documents = $documentModel->getAllWithDetails();
        } else {
            $documents = $documentModel->getByUserId($userId);
        }

        $contentView = PATH_VIEW . 'documents/index.php';
        require_once PATH_VIEW_MAIN;
    }

    public function upload(): void
    {
        require_once PATH_MODEL . 'DocumentModel.php';
        $documentModel = new DocumentModel();
        $documentTypes = $documentModel->getAllDocumentTypes();
        $contentView   = PATH_VIEW . 'documents/upload.php';

        require_once PATH_VIEW_MAIN;
    }

    public function store(): void
    {
        $userId         = (int)($_SESSION['user_id'] ?? 0);
        $documentTypeId = (int)($_POST['document_type_id'] ?? 0);
        $documentName   = trim($_POST['document_name'] ?? '');
        $description    = trim($_POST['description'] ?? '');

        require_once PATH_MODEL . 'EmployeeModel.php';
        require_once PATH_MODEL . 'DocumentModel.php';

        $employeeModel = new EmployeeModel();
        $documentModel = new DocumentModel();
        $employee      = $employeeModel->findByUserId($userId);

        if (!$employee) {
            header('Location: index.php?page=documents&action=upload');
            exit;
        }

        $filePath = '';
        if (!empty($_FILES['document_file']['name']) && $_FILES['document_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = PATH_ASSETS_UPLOADS . 'documents/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $ext      = pathinfo($_FILES['document_file']['name'], PATHINFO_EXTENSION);
            $safeName = 'doc_' . (int)$employee['id'] . '_' . time() . ($ext ? '.' . $ext : '');
            $destPath = $uploadDir . $safeName;

            if (move_uploaded_file($_FILES['document_file']['tmp_name'], $destPath)) {
                $filePath = 'assets/uploads/documents/' . $safeName;
            }
        }

        $documentModel->createDocument([
            'employee_id'      => (int)$employee['id'],
            'document_type_id' => $documentTypeId,
            'document_name'    => $documentName,
            'file_path'        => $filePath,
            'description'      => $description,
            'status'           => 'Pending',
        ]);

        header('Location: index.php?page=documents');
        exit;
    }

    public function review(): void
    {
        $roleName = $_SESSION['role_name'] ?? '';
        if (!in_array($roleName, ['admin', 'hr'], true)) {
            header('Location: index.php?page=documents');
            exit;
        }

        $id = (int)($_GET['id'] ?? 0);
        require_once PATH_MODEL . 'DocumentModel.php';
        $documentModel = new DocumentModel();
        $document      = $documentModel->findByIdWithDetails($id);
        $reviews       = $documentModel->getReviewsByDocumentId($id);
        $contentView   = PATH_VIEW . 'documents/review.php';

        require_once PATH_VIEW_MAIN;
    }

    public function storeReview(): void
    {
        $roleName = $_SESSION['role_name'] ?? '';
        if (!in_array($roleName, ['admin', 'hr'], true)) {
            header('Location: index.php?page=documents');
            exit;
        }

        $documentId = (int)($_POST['document_id'] ?? 0);
        $reviewerId = (int)($_SESSION['user_id'] ?? 0);
        $action     = $_POST['action'] ?? 'Reviewing';
        $comment    = trim($_POST['comment'] ?? '');

        $allowedActions = ['Reviewing', 'Need Update', 'Approved', 'Rejected'];
        if (!in_array($action, $allowedActions, true)) {
            $action = 'Reviewing';
        }

        require_once PATH_MODEL . 'DocumentModel.php';
        require_once PATH_MODEL . 'NotificationModel.php';

        $documentModel     = new DocumentModel();
        $notificationModel = new NotificationModel();

        $document = $documentModel->findByIdWithDetails($documentId);
        if ($document) {
            $documentModel->updateStatus($documentId, $action);
            $documentModel->addReview([
                'document_id' => $documentId,
                'reviewer_id' => $reviewerId,
                'action'      => $action,
                'comment'     => $comment,
            ]);

            if (!empty($document['owner_user_id'])) {
                $notificationModel->createNotification([
                    'user_id' => (int)$document['owner_user_id'],
                    'title'   => 'Cập nhật trạng thái hồ sơ: ' . $action,
                    'message' => $comment !== '' ? $comment : ('Hồ sơ "' . $document['document_name'] . '" đang ở trạng thái ' . $action),
                    'is_read' => 0,
                ]);
            }
        }

        header('Location: index.php?page=documents');
        exit;
    }
}