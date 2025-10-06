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
        $query = "SELECT id, title, message, read, created_at, link FROM notifications FROM " . $this->table_name . "
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
                  WHERE user_id = :user_id AND read = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function markAllRead($user_id)
    {
        $query = "UPDATE " . $this->table_name . "
                  SET read = 1
                  WHERE user_id = :user_id AND read = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}