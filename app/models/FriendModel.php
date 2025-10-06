<?php
class FriendModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Tìm kiếm user theo tên (cho gợi ý tìm kiếm)
    public function searchUsers($query, $current_user_id)
    {
        $search = "%$query%";
        $sql = "
            SELECT id, username, fullname, role, avatar
            FROM account
            WHERE (username LIKE ? OR fullname LIKE ?)
            AND id != ?
            AND id NOT IN (
                SELECT CASE WHEN user_id1 = ? THEN user_id2 ELSE user_id1 END
                FROM friends 
                WHERE user_id1 = ? OR user_id2 = ?
                AND status = 'accepted'
            )
            ORDER BY fullname ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$search, $search, $current_user_id, $current_user_id, $current_user_id, $current_user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả user (trừ user hiện tại và bạn bè)
    public function getAllUsers($current_user_id)
    {
        $sql = "
            SELECT id, username, fullname, role, avatar
            FROM account
            WHERE id != ?
            AND id NOT IN (
                SELECT CASE WHEN user_id1 = ? THEN user_id2 ELSE user_id1 END
                FROM friends 
                WHERE user_id1 = ? OR user_id2 = ?
                AND status = 'accepted'
            )
            ORDER BY fullname ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$current_user_id, $current_user_id, $current_user_id, $current_user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Gửi yêu cầu kết bạn
    public function sendFriendRequest($sender_id, $receiver_id)
    {
        // Kiểm tra nếu đã tồn tại request
        $stmt = $this->db->prepare("
            SELECT id FROM friends
            WHERE (user_id1 = ? AND user_id2 = ?) OR (user_id1 = ? AND user_id2 = ?)
        ");
        $stmt->execute([$sender_id, $receiver_id, $receiver_id, $sender_id]);
        if ($stmt->rowCount() > 0) {
            return false; // Đã tồn tại
        }

        $stmt = $this->db->prepare("
            INSERT INTO friends (user_id1, user_id2, status)
            VALUES (?, ?, 'pending')
        ");
        $result = $stmt->execute([$sender_id, $receiver_id]);
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    /**
     * Xóa bạn bè
     * @param int $user_id ID của người dùng
     * @param int $friend_id ID của bạn bè
     * @return array Kết quả
     */
    public function removeFriend($user_id, $friend_id)
    {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM friends 
                WHERE ((user_id1 = :user_id AND user_id2 = :friend_id) 
                OR (user_id1 = :friend_id AND user_id2 = :user_id))
                AND status = 'accepted'
            ");
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':friend_id', $friend_id, PDO::PARAM_INT);
            $stmt->execute();
            return ['success' => $stmt->rowCount() > 0, 'message' => $stmt->rowCount() > 0 ? 'Xóa bạn bè thành công!' : 'Không tìm thấy quan hệ bạn bè!'];
        } catch (PDOException $e) {
            error_log("Error in removeFriend: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi khi xóa bạn bè: ' . $e->getMessage()];
        }
    }

    // Chấp nhận yêu cầu
    public function acceptFriendRequest($friend_id, $user_id)
    {
        $stmt = $this->db->prepare("
            UPDATE friends SET status = 'accepted', updated_at = NOW()
            WHERE id = ? AND (user_id1 = ? OR user_id2 = ?)
        ");
        return $stmt->execute([$friend_id, $user_id, $user_id]);
    }

    // Từ chối yêu cầu
    public function rejectFriendRequest($friend_id, $user_id)
    {
        $stmt = $this->db->prepare("
            UPDATE friends SET status = 'rejected', updated_at = NOW()
            WHERE id = ? AND (user_id1 = ? OR user_id2 = ?)
        ");
        return $stmt->execute([$friend_id, $user_id, $user_id]);
    }

    // Lấy danh sách bạn bè
    public function getFriends($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT a.id, a.username, a.fullname, a.role, a.avatar
            FROM account a
            INNER JOIN friends f ON 
                (f.user_id1 = a.id AND f.user_id2 = ?) OR 
                (f.user_id2 = a.id AND f.user_id1 = ?)
            WHERE f.status = 'accepted'
        ");
        $stmt->execute([$user_id, $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getFriendsList($user_id)
    {
        $stmt = $this->db->prepare("
            SELECT a.id, a.username, a.fullname, a.avatar
            FROM account a
            INNER JOIN friends f ON (f.user_id1 = a.id OR f.user_id2 = a.id)
            WHERE (f.user_id1 = ? OR f.user_id2 = ?) 
            AND f.status = 'accepted' 
            AND a.id != ?
        ");
        $stmt->execute([$user_id, $user_id, $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
