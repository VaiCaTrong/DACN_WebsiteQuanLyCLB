<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/helpers/SessionHelper.php';
SessionHelper::start();
SessionHelper::requireLogin();

// Giả định notifications được lấy từ AccountController hoặc model
$notifications = $notifications ?? [];
$user_id = SessionHelper::getUserId();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Báo Của Tôi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #E91E63;
            --primary-light: #FCE4EC;
            --primary-dark: #C2185B;
            --success: #00b894;
            --danger: #d63031;
            --warning: #fdcb6e;
            --bg-light: #f8f9fa;
            --text-dark: #2d3436;
            --text-light: #636e72;
        }

        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 0.75rem 0;
            margin-bottom: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
        }

        .notification-card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 0.75rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .notification-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .notification-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 0.5rem 1rem;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .notification-body {
            padding: 0.75rem 1rem;
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        .notification-time {
            font-size: 0.7rem;
            color: var(--text-light);
        }

        .notification-link {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .notification-link:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        .delete-btn, .delete-all-btn {
            color: var(--danger);
            font-size: 0.8rem;
            padding: 0.2rem 0.5rem;
            border: none;
            background: none;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .delete-btn:hover, .delete-all-btn:hover {
            color: darken(var(--danger), 10%);
        }
    </style>
</head>

<body>
    <div class="container my-4">
        <div class="header">
            <h1><i class="fas fa-bell me-2"></i>Thông Báo Của Tôi</h1>
            <button class="delete-all-btn" onclick="confirmDeleteAll()"><i class="fas fa-trash me-1"></i>Xóa tất cả</button>
        </div>

        <?php if (empty($notifications)): ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash fa-3x mb-3"></i>
                <p>Bạn chưa có thông báo nào.</p>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification-card" data-id="<?php echo htmlspecialchars($notification['id']); ?>">
                    <div class="notification-header">
                        <span class="notification-title"><i class="fas fa-info-circle me-1"></i><?php echo htmlspecialchars($notification['title']); ?></span>
                        <button class="delete-btn" onclick="confirmDelete(<?php echo $notification['id']; ?>)"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="notification-body">
                        <p class="mb-2"><?php echo htmlspecialchars($notification['message']); ?></p>
                        <?php if (!empty($notification['friend_id'])): ?>
                            <button class="btn btn-sm btn-primary open-friend-request-modal" 
                                    data-friend-id="<?php echo $notification['friend_id']; ?>" 
                                    data-notification-id="<?php echo $notification['id']; ?>">
                                Xem yêu cầu kết bạn
                            </button>
                        <?php endif; ?>
                        <small class="notification-time"><i class="fas fa-clock me-1"></i><?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?></small>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Modal xác nhận xóa -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa thông báo này?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Xóa</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal xóa tất cả -->
    <div class="modal fade" id="deleteAllModal" tabindex="-1" aria-labelledby="deleteAllModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAllModalLabel">Xác nhận xóa tất cả</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có chắc chắn muốn xóa tất cả thông báo?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteAllBtn">Xóa tất cả</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal xác nhận yêu cầu kết bạn -->
    <div class="modal fade" id="friendRequestModal" tabindex="-1" aria-labelledby="friendRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="friendRequestModalLabel">Xác nhận yêu cầu kết bạn</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Bạn có muốn chấp nhận yêu cầu kết bạn này?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-success" id="acceptFriendBtn">Chấp nhận</button>
                    <button type="button" class="btn btn-danger" id="rejectFriendBtn">Từ chối</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentNotificationId = null;
        let currentFriendId = null;

        function confirmDelete(notificationId) {
            currentNotificationId = notificationId;
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        function confirmDeleteAll() {
            const modal = new bootstrap.Modal(document.getElementById('deleteAllModal'));
            modal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (currentNotificationId) {
                fetch('/webdacn_quanlyclb/account/deleteNotification', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo htmlspecialchars(SessionHelper::getCsrfToken()); ?>'
                    },
                    body: JSON.stringify({ notification_id: currentNotificationId, user_id: <?php echo $user_id; ?> })
                })
                .then(response => response.json())
                .then(data => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    modal.hide();
                    if (data.success) {
                        const card = document.querySelector(`.notification-card[data-id="${currentNotificationId}"]`);
                        if (card) card.remove();
                        if (document.querySelectorAll('.notification-card').length === 0) {
                            location.reload();
                        }
                    } else {
                        alert('Có lỗi xảy ra khi xóa thông báo.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi gửi yêu cầu.');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                    modal.hide();
                });
            }
        });

        document.getElementById('confirmDeleteAllBtn').addEventListener('click', function() {
            fetch('/webdacn_quanlyclb/account/deleteAllNotifications', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?php echo htmlspecialchars(SessionHelper::getCsrfToken()); ?>'
                },
                body: JSON.stringify({ user_id: <?php echo $user_id; ?> })
            })
            .then(response => response.json())
            .then(data => {
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteAllModal'));
                modal.hide();
                if (data.success) {
                    location.reload();
                } else {
                    alert('Có lỗi xảy ra khi xóa tất cả thông báo.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi gửi yêu cầu.');
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteAllModal'));
                modal.hide();
            });
        });

        // Mở modal xác nhận yêu cầu kết bạn
        document.querySelectorAll('.open-friend-request-modal').forEach(button => {
            button.addEventListener('click', function() {
                currentFriendId = this.dataset.friendId;
                currentNotificationId = this.dataset.notificationId;
                const modal = new bootstrap.Modal(document.getElementById('friendRequestModal'));
                modal.show();
            });
        });

        // Xử lý chấp nhận kết bạn
        document.getElementById('acceptFriendBtn').addEventListener('click', function() {
            if (currentFriendId && currentNotificationId) {
                fetch('/webdacn_quanlyclb/friend/acceptFriend', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo htmlspecialchars(SessionHelper::getCsrfToken()); ?>'
                    },
                    body: JSON.stringify({ friend_id: currentFriendId, notification_id: currentNotificationId })
                })
                .then(response => response.json())
                .then(data => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('friendRequestModal'));
                    modal.hide();
                    if (data.success) {
                        alert('Đã chấp nhận kết bạn!');
                        const card = document.querySelector(`.notification-card[data-id="${currentNotificationId}"]`);
                        if (card) card.remove();
                        if (document.querySelectorAll('.notification-card').length === 0) {
                            location.reload();
                        }
                    } else {
                        alert('Lỗi khi chấp nhận.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi chấp nhận yêu cầu kết bạn.');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('friendRequestModal'));
                    modal.hide();
                });
            }
        });

        // Xử lý từ chối kết bạn
        document.getElementById('rejectFriendBtn').addEventListener('click', function() {
            if (currentFriendId && currentNotificationId) {
                fetch('/webdacn_quanlyclb/friend/rejectFriend', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': '<?php echo htmlspecialchars(SessionHelper::getCsrfToken()); ?>'
                    },
                    body: JSON.stringify({ friend_id: currentFriendId, notification_id: currentNotificationId })
                })
                .then(response => response.json())
                .then(data => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('friendRequestModal'));
                    modal.hide();
                    if (data.success) {
                        alert('Đã từ chối kết bạn!');
                        const card = document.querySelector(`.notification-card[data-id="${currentNotificationId}"]`);
                        if (card) card.remove();
                        if (document.querySelectorAll('.notification-card').length === 0) {
                            location.reload();
                        }
                    } else {
                        alert('Lỗi khi từ chối.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Có lỗi xảy ra khi từ chối yêu cầu kết bạn.');
                    const modal = bootstrap.Modal.getInstance(document.getElementById('friendRequestModal'));
                    modal.hide();
                });
            }
        });
    </script>
</body>
</html>