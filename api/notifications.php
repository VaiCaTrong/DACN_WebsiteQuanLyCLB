<?php
require_once '../../config/autoload.php';
require_once '../../app/models/NotificationModel.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();
$notificationModel = new NotificationModel($db);
$user_id = SessionHelper::getUserId();

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập!']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $notifications = $notificationModel->getNotificationsByUserId($user_id);
    $unread_count = $notificationModel->getUnreadCount($user_id);
    echo json_encode([
        'success' => true,
        'notifications' => $notifications,
        'unread_count' => $unread_count
    ]);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notificationModel->markAllRead($user_id);
    echo json_encode(['success' => true]);
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
exit;
