<?php
class ChatModel
{
    private $conn;
    private $messages_table = "messages";
    private $group_messages_table = "group_messages";

    public function __construct($db)
    {
        $this->conn = $db;
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function sendPrivateMessage($sender_id, $receiver_id, $content, $message_type = 'text', $reply_to_id = null, $toxicity_score = null, $content_english = null)
    {
        try {
            $query = "INSERT INTO " . $this->messages_table . " 
                      (sender_id, receiver_id, content, content_english, message_type, reply_to_message_id, toxicity_score, timestamp) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $this->conn->prepare($query);
            $clean_content = ($message_type == 'text') ? htmlspecialchars(strip_tags($content)) : $content;
            return $stmt->execute([$sender_id, $receiver_id, $clean_content, $content_english, $message_type, $reply_to_id, $toxicity_score]);
        } catch (PDOException $e) {
            error_log("Send Private Message Error: " . $e->getMessage());
            return false;
        }
    }

    public function sendGroupMessage(
        $sender_id,
        $team_id,
        $content,
        $message_type = 'text',
        $reply_to_id = null,
        $toxicity_score = null,
        $content_english = null
    ) {
        try {
            $this->conn->beginTransaction();

            // === ÉP KIỂU TOXICITY_SCORE THÀNH FLOAT NGAY TỪ ĐẦU ===
            $toxicity_score = $toxicity_score !== null ? (float)$toxicity_score : null;

            // === KIỂM TRA CÓ BỊ CENSOR KHÔNG (có *** không) ===
            $has_censored_word = ($message_type === 'text' && preg_match('/\*{2,}/u', $content));

            // === QUYẾT ĐỊNH TRỪ ĐIỂM HAY KHÔNG ===
            $should_deduct = false;

            if ($has_censored_word) {
                $should_deduct = true; // Bị censor → chắc chắn trừ điểm
            } elseif ($toxicity_score !== null && $toxicity_score > 0.5) {
                $should_deduct = true; // AI phát hiện toxic → trừ điểm
            }

            // === TÍNH ĐIỂM SỨC KHỎE NHÓM ===
            $delta = 0;

            if ($should_deduct) {
                $delta = -1; // Trừ 1 điểm nếu vi phạm
            } else {
                // Chỉ cộng điểm nếu tin nhắn sạch và AI chấm tốt (≤ 0.5 và > 0)
                if ($toxicity_score !== null && $toxicity_score > 0 && $toxicity_score <= 0.5) {
                    $delta = +1;
                }
                // Nếu toxicity_score = 0 hoặc null → giữ nguyên (không cộng, không trừ)
            }

            // Insert tin nhắn
            $query = "INSERT INTO group_messages 
                  (sender_id, team_id, content, content_english, message_type, 
                   reply_to_message_id, toxicity_score, timestamp) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

            $stmt = $this->conn->prepare($query);
            $clean_content = ($message_type == 'text') ? htmlspecialchars(strip_tags($content)) : $content;

            $stmt->execute([
                $sender_id,
                $team_id,
                $clean_content,
                $content_english,
                $message_type,
                $reply_to_id,
                $toxicity_score
            ]);

            // Cập nhật điểm nhóm nếu có thay đổi
            if ($delta !== 0) {
                $this->conn->prepare("
                UPDATE team 
                SET group_health_score = GREATEST(0, LEAST(100, group_health_score + ?))
                WHERE id = ?
            ")->execute([$delta, $team_id]);
            }

            // Kiểm tra khóa nhóm nếu điểm ≤ 0
            $scoreStmt = $this->conn->prepare("SELECT group_health_score FROM team WHERE id = ?");
            $scoreStmt->execute([$team_id]);
            $currentScore = (int)$scoreStmt->fetchColumn();

            if ($currentScore <= 0) {
                $this->conn->prepare("
                UPDATE team 
                SET is_chat_locked = 1, 
                    group_health_score = 0 
                WHERE id = ?
            ")->execute([$team_id]);
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Send Group Message Error: " . $e->getMessage());
            return false;
        }
    }

    public function getPrivateMessages($user1_id, $user2_id)
    {
        try {
            $query = "SELECT m.id, m.content, m.message_type, m.timestamp, m.sender_id, m.deleted_at, m.reply_to_message_id, m.toxicity_score,
                         a_sender.fullname as sender_fullname,
                         replied.content as replied_content,
                         replied.message_type as replied_message_type,
                         a_replied_sender.fullname as replied_sender_fullname
                  FROM " . $this->messages_table . " m
                  JOIN account a_sender ON m.sender_id = a_sender.id
                  LEFT JOIN " . $this->messages_table . " replied ON m.reply_to_message_id = replied.id
                  LEFT JOIN account a_replied_sender ON replied.sender_id = a_replied_sender.id
                  WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
                  ORDER BY m.timestamp ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user1_id, $user2_id, $user2_id, $user1_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Private Messages Error: " . $e->getMessage());
            return [];
        }
    }

    public function getGroupMessages($team_id)
    {
        try {
            $query = "SELECT gm.id, gm.content, gm.message_type, gm.timestamp, gm.sender_id, gm.deleted_at, gm.reply_to_message_id, gm.toxicity_score,
                         a_sender.fullname as sender_fullname,
                         replied.content as replied_content,
                         replied.message_type as replied_message_type,
                         a_replied_sender.fullname as replied_sender_fullname
                  FROM " . $this->group_messages_table . " gm
                  JOIN account a_sender ON gm.sender_id = a_sender.id
                  LEFT JOIN " . $this->group_messages_table . " replied ON gm.reply_to_message_id = replied.id
                  LEFT JOIN account a_replied_sender ON replied.sender_id = a_replied_sender.id
                  WHERE gm.team_id = ?
                  ORDER BY gm.timestamp ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$team_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Group Messages Error: " . $e->getMessage());
            return [];
        }
    }

    public function checkNewMessages($user_id, $last_check_timestamp)
    {
        try {
            $query = "SELECT COUNT(*) as new_message_count
                      FROM (
                          SELECT timestamp, is_read FROM " . $this->messages_table . " WHERE receiver_id = ?
                          UNION
                          SELECT timestamp, is_read FROM " . $this->group_messages_table . " gm
                          INNER JOIN user_team ut ON gm.team_id = ut.team_id
                          WHERE ut.user_id = ?
                      ) AS all_messages
                      WHERE timestamp > ? AND (is_read = 0 OR is_read IS NULL)";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user_id, $user_id, $last_check_timestamp]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return $result['new_message_count'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getUserTeams($user_id)
    {
        try {
            $query = "SELECT t.id, t.name, t.avatar_team, t.user_id as leader_id, t.is_chat_locked, t.group_health_score
                      FROM team t
                      INNER JOIN user_team ut ON t.id = ut.team_id
                      WHERE ut.user_id = ?";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function isUserInTeam($user_id, $team_id)
    {
        if (empty($team_id)) return false;
        try {
            $query = "SELECT COUNT(*) as count FROM user_team WHERE team_id = ? AND user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$team_id, $user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deleteMessage($messageId, $userId, $messageType)
    {
        $table = ($messageType === 'group') ? $this->group_messages_table : $this->messages_table;
        try {
            $query = "UPDATE `{$table}` SET deleted_at = NOW() WHERE id = ? AND sender_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$messageId, $userId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Mở khóa nhóm chat và reset điểm sức khỏe về 50.
     * (HÀM MỚI THÊM VÀO)
     */
    public function unlockGroupChat($team_id)
    {
        try {
            $query = "UPDATE team 
                      SET is_chat_locked = 0, 
                          group_health_score = 50 
                      WHERE id = :team_id";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([':team_id' => $team_id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Unlock Group Chat Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh sách các nhóm bị khóa
     * (HÀM MỚI THÊM VÀO - Để hiển thị trong locked.php)
     */
    public function getLockedGroups()
    {
        try {
            $query = "SELECT t.id, t.name, t.group_health_score, a.fullname as leader_name
                      FROM team t
                      LEFT JOIN account a ON t.user_id = a.id
                      WHERE t.is_chat_locked = 1";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Xóa toàn bộ lịch sử chat riêng giữa 2 người.
     */
    public function clearPrivateChatHistory($user1_id, $user2_id)
    {
        try {
            $query = "DELETE FROM " . $this->messages_table . " 
                      WHERE (sender_id = ? AND receiver_id = ?) 
                         OR (sender_id = ? AND receiver_id = ?)";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$user1_id, $user2_id, $user2_id, $user1_id]);
        } catch (PDOException $e) {
            error_log("Clear Private Chat History Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa toàn bộ lịch sử chat nhóm.
     */
    public function clearGroupChatHistory($team_id)
    {
        try {
            $query = "DELETE FROM " . $this->group_messages_table . " 
                      WHERE team_id = ?";
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$team_id]);
        } catch (PDOException $e) {
            error_log("Clear Group Chat History Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Kiểm tra xem người dùng có phải là trưởng nhóm không.
     */
    public function isUserTeamLeader($user_id, $team_id)
    {
        try {
            // user_id trong bảng team là leader_id
            $query = "SELECT COUNT(*) FROM team WHERE id = ? AND user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$team_id, $user_id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Is User Team Leader Error: " . $e->getMessage());
            return false;
        }
    }
}
