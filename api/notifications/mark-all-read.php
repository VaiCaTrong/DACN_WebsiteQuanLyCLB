<?php
session_start();
header('Content-Type: application/json');
// Kiểm tra CSRF token (tùy chọn)
if ($_SERVER['HTTP_X_CSRF_TOKEN'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'CSRF token không hợp lệ']);
    exit;
}

// Giả định dữ liệu từ database
$notifications = [
    ['title' => 'Yêu cầu mới', 'message' => 'Có yêu cầu từ user123', 'read' => false, 'created_at' => date('Y-m-d H:i:s')],
    // ... thêm dữ liệu
];
$unread_count = count(array_filter($notifications, fn($n) => !$n['read']));
echo json_encode(['success' => true, 'unread_count' => $unread_count, 'notifications' => $notifications]);
?>