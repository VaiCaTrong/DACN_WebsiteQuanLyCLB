<?php
class NotificationModel
{
    private $conn;
    private $table_name = "notifications";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getNotificationsByUserId($user_id, $limit = 10)
    {
        $query = "SELECT id, title, message, `read`, created_at, link FROM " . $this->table_name . "
                  WHERE user_id = :user_id
                  ORDER BY created_at DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadCount($user_id)
    {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . "
                  WHERE user_id = :user_id AND `read` = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function markAllRead($user_id)
    {
        $query = "UPDATE " . $this->table_name . "
                  SET `read` = 1
                  WHERE user_id = :user_id AND `read` = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function markAsRead($notification_id, $user_id)
    {
        $query = "UPDATE " . $this->table_name . "
                  SET `read` = 1
                  WHERE id = :notification_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':notification_id', $notification_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteNotification($notification_id, $user_id)
    {
        $query = "DELETE FROM " . $this->table_name . "
                  WHERE id = :notification_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':notification_id', $notification_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function clearAll($user_id)
    {
        $query = "DELETE FROM " . $this->table_name . "
                  WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getUserNotifications($user_id) {
        // Sửa query để phù hợp với cấu trúc bảng thực tế
        $query = "
            SELECT n.*, 
                   NULL as sender_name, 
                   NULL as sender_avatar,
                   CASE 
                     WHEN n.friend_id IS NOT NULL THEN 'friend_request'
                     ELSE 'system'
                   END as type
            FROM " . $this->table_name . " n
            WHERE n.user_id = :user_id
            ORDER BY n.created_at DESC
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':user_id' => $user_id]);
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Thêm thông tin sender nếu có friend_id
        foreach ($notifications as &$notification) {
            if (!empty($notification['friend_id'])) {
                $senderInfo = $this->getSenderInfo($notification['friend_id'], $user_id);
                if ($senderInfo) {
                    $notification['sender_id'] = $senderInfo['id'];
                    $notification['sender_name'] = $senderInfo['fullname'];
                    $notification['sender_avatar'] = $senderInfo['avatar'];
                }
            }
        }
        
        return $notifications;
    }

    private function getSenderInfo($friend_id, $user_id) {
        $query = "
            SELECT a.id, a.fullname, a.avatar 
            FROM account a 
            INNER JOIN friends f ON (f.user_id1 = a.id OR f.user_id2 = a.id)
            WHERE f.id = :friend_id 
            AND (f.user_id1 = :user_id OR f.user_id2 = :user_id)
            AND a.id != :user_id
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':friend_id' => $friend_id,
            ':user_id' => $user_id
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>