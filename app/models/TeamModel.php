<?php
require_once 'app/config/database.php';

class TeamModel
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function getAllTeams()
    {
        $stmt = $this->db->prepare("SELECT * FROM team");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function isUserInTeam($user_id, $team_id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM user_team WHERE user_id = :user_id AND team_id = :team_id");
        $stmt->execute([':user_id' => $user_id, ':team_id' => $team_id]);
        return $stmt->fetchColumn() > 0;
    }

    public function getTeamById($team_id)
    {
        $stmt = $this->db->prepare("
            SELECT t.*, a.fullname AS creator_name
            FROM team t
            LEFT JOIN account a ON t.user_id = a.id
            WHERE t.id = :team_id
        ");
        $stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Thêm vào FriendModel
    public function getSuggestedUsers($userId, $limit = 6)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT id, username, fullname, email, avatar, role 
            FROM account 
            WHERE id != :user_id
            AND id NOT IN (
                SELECT 
                    CASE 
                        WHEN user_id = :user_id THEN friend_id 
                        WHEN friend_id = :user_id THEN user_id 
                    END 
                FROM friends 
                WHERE (user_id = :user_id OR friend_id = :user_id)
            )
            ORDER BY RAND()
            LIMIT :limit
        ");
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy gợi ý kết bạn: " . $e->getMessage());
            return [];
        }
    }

    public function addTeam($name, $description, $quantity_user, $talent, $note, $user_id, $avatar_team = null)
    {
        $stmt = $this->db->prepare("INSERT INTO team (name, description, quantity_user, talent, note, user_id, avatar_team) VALUES (:name, :description, :quantity_user, :talent, :note, :user_id, :avatar_team)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':quantity_user', $quantity_user, PDO::PARAM_INT);
        $stmt->bindParam(':talent', $talent);
        $stmt->bindParam(':note', $note);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':avatar_team', $avatar_team);
        return $stmt->execute();
    }

    public function updateTeam($id, $name, $description, $quantity_user, $talent, $note, $user_id, $avatar_team = null)
    {
        $stmt = $this->db->prepare("UPDATE team SET name = :name, description = :description, quantity_user = :quantity_user, talent = :talent, note = :note, user_id = :user_id, avatar_team = :avatar_team WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':quantity_user', $quantity_user, PDO::PARAM_INT);
        $stmt->bindParam(':talent', $talent);
        $stmt->bindParam(':note', $note);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':avatar_team', $avatar_team);
        return $stmt->execute();
    }

    public function deleteTeam($id)
    {
        $stmt = $this->db->prepare("DELETE FROM team WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getUserTeam($user_id)
    {
        $stmt = $this->db->prepare("SELECT t.* FROM team t JOIN account a ON t.id = a.team_id WHERE a.id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTeamMembers($team_id)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, ut.point
            FROM account a
            LEFT JOIN user_team ut ON a.id = ut.user_id
            WHERE a.team_id = :team_id
        ");
        $stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // public function createTeamRequest($data)
    // {
    //     $stmt = $this->db->prepare("INSERT INTO team_requests (team_id, user_id, name, khoa, reason, talent, created_at, avatar_team) VALUES (:team_id, :user_id, :name, :khoa, :reason, :talent, :created_at, :avatar_team)");
    //     $stmt->bindParam(':team_id', $data['team_id']);
    //     $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
    //     $stmt->bindParam(':name', $data['name']);
    //     $stmt->bindParam(':khoa', $data['khoa']);
    //     $stmt->bindParam(':reason', $data['reason']);
    //     $stmt->bindParam(':talent', $data['talent']);
    //     $stmt->bindParam(':created_at', $data['created_at']);
    //     $stmt->bindParam(':avatar_team', $data['avatar_team']);
    //     return $stmt->execute();
    // }
    public function createTeamRequest($data)
    {
        $sql = "INSERT INTO team_requests (team_id, user_id, name, khoa, reason, talent, created_at, avatar_team)
            VALUES (:team_id, :user_id, :name, :khoa, :reason, :talent, :created_at, :avatar_team)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }


    public function getAllTeamRequests()
    {
        $stmt = $this->db->prepare("SELECT * FROM team_requests");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeamRequestById($team_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM team_requests WHERE team_id = :team_id");
        $stmt->bindParam(':team_id', $team_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteTeamRequest($team_id)
    {
        $stmt = $this->db->prepare("DELETE FROM team_requests WHERE team_id = :team_id");
        $stmt->bindParam(':team_id', $team_id);
        return $stmt->execute();
    }

    public function getPendingRequests()
    {
        $stmt = $this->db->prepare("
            SELECT t.*, a.fullname AS creator_name
            FROM team_requests t
            LEFT JOIN account a ON t.user_id = a.id
            ORDER BY t.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy ID đội hiện tại của người dùng
     * @param int $user_id
     * @return mixed
     */
    public function getCurrentTeamId($user_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT team_id FROM account WHERE id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() ?: null;
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy team_id: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lưu yêu cầu tham gia đội với các trường mở rộng
     * @param int $user_id
     * @param int $team_id
     * @param string $name
     * @param string $date_of_birth
     * @param string $khoa
     * @param string $reason
     * @param string|null $talent
     * @return bool
     */
    public function saveJoinRequest($user_id, $team_id, $name, $date_of_birth, $khoa, $reason, $talent = null)
    {
        try {
            // Lấy leader_id từ bảng team
            $stmt = $this->db->prepare("SELECT user_id FROM team WHERE id = :team_id");
            $stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
            $stmt->execute();
            $leader_id = $stmt->fetchColumn();

            if (!$leader_id) {
                error_log("Không tìm thấy leader cho team_id: " . $team_id);
                return false;
            }

            $stmt = $this->db->prepare("INSERT INTO join_form (user_id, team_id, name, date_of_birth, khoa, reason, talent, status, created_at, leader) VALUES (:user_id, :team_id, :name, :date_of_birth, :khoa, :reason, :talent, 'pending', NOW(), :leader)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':team_id' => $team_id,
                ':name' => $name,
                ':date_of_birth' => $date_of_birth,
                ':khoa' => $khoa,
                ':reason' => $reason,
                ':talent' => $talent,
                ':leader' => $leader_id
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi khi lưu yêu cầu tham gia: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tất cả các phiếu gia nhập từ join_form
     * @return array
     */
    public function getAllJoinRequests()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM join_form ORDER BY created_at DESC");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy danh sách phiếu gia nhập: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Đếm số lượng thành viên của đội từ user_team
     * @param int $team_id
     * @return int
     */
    public function countTeamMembers($team_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM user_team WHERE team_id = :team_id");
            $stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Lỗi khi đếm thành viên: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Từ chối và xóa phiếu gia nhập
     * @param int $join_form_id
     * @return bool
     */
    public function rejectJoinRequest($join_form_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM join_form WHERE id = :id");
            return $stmt->execute([':id' => $join_form_id]);
        } catch (PDOException $e) {
            error_log("Lỗi khi từ chối yêu cầu: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Đánh dấu phiếu chờ phỏng vấn
     * @param int $join_form_id
     * @return bool
     */
    public function scheduleInterview($join_form_id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE join_form SET status = 'interview' WHERE id = :id");
            return $stmt->execute([':id' => $join_form_id]);
        } catch (PDOException $e) {
            error_log("Lỗi khi hẹn phỏng vấn: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Chuyển yêu cầu từ join_form sang user_team khi được phê duyệt
     * @param int $join_form_id
     * @return bool
     */
    public function approveJoinRequest($join_form_id, $approver_user_id)
    {
        try {
            $this->db->beginTransaction();

            // Lấy thông tin phiếu
            $stmt = $this->db->prepare("SELECT user_id, team_id FROM join_form WHERE id = :id AND status = 'pending'");
            $stmt->execute([':id' => $join_form_id]);
            $request = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($request) {
                $user_id = $request['user_id'];
                $team_id_from_form = $request['team_id']; // Lấy team_id từ phiếu

                // Cập nhật team_id vào bảng account
                $this->db->prepare("UPDATE account SET team_id = :team_id WHERE id = :user_id")->execute([
                    ':team_id' => $team_id_from_form,
                    ':user_id' => $user_id
                ]);

                // Chuyển sang user_team với team_id từ phiếu và điểm mặc định 100
                $this->db->prepare("DELETE FROM user_team WHERE user_id = :user_id")->execute([':user_id' => $user_id]); // Xóa đội cũ nếu có
                $this->db->prepare("INSERT INTO user_team (user_id, team_id, point) VALUES (:user_id, :team_id, :point)")->execute([
                    ':user_id' => $user_id,
                    ':team_id' => $team_id_from_form,
                    ':point' => 100
                ]);

                // Xóa phiếu
                $this->db->prepare("DELETE FROM join_form WHERE id = :id")->execute([':id' => $join_form_id]);

                $this->db->commit();
                return true;
            }
            $this->db->rollBack();
            return false;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Lỗi khi phê duyệt yêu cầu: " . $e->getMessage());
            return false;
        }
    }

    public function getTeamIdFromAccount($user_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT team_id FROM account WHERE id = :user_id LIMIT 1");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy team_id từ account: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Lấy danh sách tin nhắn của đội
     * @param int $team_id
     * @return array
     */
    public function getTeamMessages($team_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT content FROM team_messages WHERE team_id = :team_id ORDER BY created_at DESC LIMIT 10");
            $stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy tin nhắn: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Gửi tin nhắn cho đội
     * @param int $user_id
     * @param int $team_id
     * @param string $message
     * @return bool
     */
    public function sendTeamMessage($user_id, $team_id, $message)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO team_messages (user_id, team_id, content, created_at) VALUES (:user_id, :team_id, :content, NOW())");
            return $stmt->execute([
                ':user_id' => $user_id,
                ':team_id' => $team_id,
                ':content' => $message
            ]);
        } catch (PDOException $e) {
            error_log("Lỗi khi gửi tin nhắn: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cho phép người dùng rời câu lạc bộ
     * @param int $user_id
     * @return bool
     */
    public function leaveTeam($user_id)
    {
        try {
            $this->db->beginTransaction();

            $current_team_id = $this->getCurrentTeamId($user_id);
            if ($current_team_id) {
                // Xóa bản ghi khỏi user_team
                $this->db->prepare("DELETE FROM user_team WHERE user_id = :user_id AND team_id = :team_id")->execute([
                    ':user_id' => $user_id,
                    ':team_id' => $current_team_id
                ]);

                // Đặt lại team_id trong account thành NULL
                $this->db->prepare("UPDATE account SET team_id = NULL WHERE id = :user_id")->execute([
                    ':user_id' => $user_id
                ]);

                $this->db->commit();
                return true;
            }
            $this->db->rollBack();
            return false;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Lỗi khi rời đội: " . $e->getMessage());
            return false;
        }
    }

    public function getTeamByUserId($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM team WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getJoinRequestsForTeam($team_id)
    {
        $stmt = $this->db->prepare("
        SELECT j.*, a.fullname, a.email 
        FROM join_form j
        JOIN account a ON j.user_id = a.id
        WHERE j.team_id = :team_id AND j.status = 'pending'
        ORDER BY j.created_at DESC
    ");
        $stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function punishMember($user_id, $team_id, $reason, $severity, $created_by)
    {
        try {
            $this->db->beginTransaction();

            $points_deducted = $severity === 'light' ? 5 : ($severity === 'medium' ? 10 : 15);

            $stmt = $this->db->prepare("UPDATE user_team SET point = point - :points WHERE user_id = :user_id AND team_id = :team_id");
            $stmt->execute([
                ':points' => $points_deducted,
                ':user_id' => $user_id,
                ':team_id' => $team_id
            ]);

            $stmt = $this->db->prepare("INSERT INTO punishments (user_id, team_id, reason, severity, points_deducted, created_at, created_by) VALUES (:user_id, :team_id, :reason, :severity, :points_deducted, NOW(), :created_by)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':team_id' => $team_id,
                ':reason' => $reason,
                ':severity' => $severity,
                ':points_deducted' => $points_deducted,
                ':created_by' => $created_by
            ]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Lỗi khi xử lý phạt: " . $e->getMessage());
            return false;
        }
    }
    public function rewardMember($user_id, $team_id, $reason, $severity, $created_by)
    {
        try {
            $this->db->beginTransaction();

            $points_added = $severity === 'temporary' ? 5 : ($severity === 'good' ? 10 : 15);

            $stmt = $this->db->prepare("UPDATE user_team SET point = point + :points WHERE user_id = :user_id AND team_id = :team_id");
            $stmt->execute([
                ':points' => $points_added,
                ':user_id' => $user_id,
                ':team_id' => $team_id
            ]);

            $stmt = $this->db->prepare("INSERT INTO rewards (user_id, team_id, reason, severity, points_added, created_at, created_by) VALUES (:user_id, :team_id, :reason, :severity, :points_added, NOW(), :created_by)");
            $stmt->execute([
                ':user_id' => $user_id,
                ':team_id' => $team_id,
                ':reason' => $reason,
                ':severity' => $severity,
                ':points_added' => $points_added,
                ':created_by' => $created_by
            ]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Lỗi khi xử lý thưởng: " . $e->getMessage());
            return false;
        }
    }
    public function getUserPoints($user_id, $team_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT point FROM user_team WHERE user_id = :user_id AND team_id = :team_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':team_id', $team_id, PDO::PARAM_INT);
            $stmt->execute();
            $point = $stmt->fetchColumn();
            return $point !== false ? (int)$point : 0;
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy điểm: " . $e->getMessage());
            return 0;
        }
    }
    // Trong TeamModel.php, thêm phương thức mới

    /**
     * Lấy tất cả các phiếu gia nhập theo leader
     * @param int $leader_id
     * @return array
     */
    public function getJoinRequestsByLeader($leader_id)
    {
        try {
            $stmt = $this->db->prepare("
            SELECT jf.*, t.name as team_name, a.username as requester_username
            FROM join_form jf
            LEFT JOIN team t ON jf.team_id = t.id
            LEFT JOIN account a ON jf.user_id = a.id
            WHERE jf.leader = :leader_id 
            ORDER BY jf.created_at DESC
        ");
            $stmt->bindParam(':leader_id', $leader_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy danh sách phiếu gia nhập theo leader: " . $e->getMessage());
            return [];
        }
    }
}
