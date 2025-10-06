<?php
class MessageModel
{
    private $conn;
    private $table_name = "messages";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Gửi tin nhắn - phiên bản đơn giản nhất
    public function sendMessage($sender_id, $receiver_id, $message)
    {
        $query = "INSERT INTO " . $this->table_name . " 
                  (sender_id, receiver_id, content, timestamp) 
                  VALUES (?, ?, ?, NOW())";

        $stmt = $this->conn->prepare($query);
        
        // Làm sạch tin nhắn
        $clean_message = htmlspecialchars(strip_tags($message));
        
        // Thực thi với parameters
        $result = $stmt->execute([$sender_id, $receiver_id, $clean_message]);
        
        return $result;
    }

    // Lấy tin nhắn giữa 2 user - phiên bản đơn giản
    public function getMessages($user1_id, $user2_id)
    {
        $query = "SELECT m.*, 
                         a_sender.username as sender_username,
                         a_sender.fullname as sender_fullname
                  FROM " . $this->table_name . " m
                  INNER JOIN account a_sender ON m.sender_id = a_sender.id
                  WHERE (m.sender_id = ? AND m.receiver_id = ?) 
                     OR (m.sender_id = ? AND m.receiver_id = ?)
                  ORDER BY m.timestamp ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user1_id, $user2_id, $user2_id, $user1_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>