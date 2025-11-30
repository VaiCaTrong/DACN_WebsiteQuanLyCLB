<?php
class AccountModel
{
    private $conn;
    private $table_name = "account";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy tài khoản theo username
    public function getAccountByUsername($username)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Lấy tài khoản theo email
    // public function getAccountByEmail($email)
    // {
    //     $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->bindParam(":email", $email);
    //     $stmt->execute();
    //     return $stmt->fetch(PDO::FETCH_OBJ);
    // }

    // Tạo tài khoản mới
    public function save($username, $fullName, $password, $role = 'user', $email = null, $phone = null, $avatar = null)
    {
        if ($this->getAccountByUsername($username)) {
            return false;
        }

        if (!empty($email) && $this->getAccountByEmail($email)) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . " 
              SET username = :username, 
                  fullname = :fullname, 
                  password = :password, 
                  role = :role,
                  email = :email,
                  phone = :phone,
                  avatar = :avatar,
                  created_at = NOW()";

        $stmt = $this->conn->prepare($query);

        $username = htmlspecialchars(strip_tags($username));
        $fullName = htmlspecialchars(strip_tags($fullName));
        $role = htmlspecialchars(strip_tags($role));
        $email = !empty($email) ? htmlspecialchars(strip_tags($email)) : null;
        $phone = !empty($phone) ? htmlspecialchars(strip_tags($phone)) : null;
        $avatar = !empty($avatar) ? htmlspecialchars(strip_tags($avatar)) : null;

        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":fullname", $fullName);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":avatar", $avatar);

        return $stmt->execute();
    }

    // Cập nhật thông tin tài khoản
    public function update($id, $username, $fullname, $password, $role, $email, $phone, $avatar)
    {
        $query = "UPDATE " . $this->table_name . "
            SET username = :username, 
                fullname = :fullname, 
                password = :password, 
                role = :role, 
                email = :email, 
                phone = :phone, 
                avatar = :avatar,
                updated_at = NOW()
            WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $username = htmlspecialchars(strip_tags($username));
        $fullname = htmlspecialchars(strip_tags($fullname));
        $role = htmlspecialchars(strip_tags($role));
        $email = !empty($email) ? htmlspecialchars(strip_tags($email)) : null;
        $phone = !empty($phone) ? htmlspecialchars(strip_tags($phone)) : null;
        $avatar = !empty($avatar) ? htmlspecialchars(strip_tags($avatar)) : null;

        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":fullname", $fullname);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":role", $role);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":avatar", $avatar);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    /**
     * Xóa tài khoản và các bản ghi liên quan trong join_form
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            $this->conn->beginTransaction();

            // Xóa các bản ghi liên quan trong join_form trước
            $stmt = $this->conn->prepare("DELETE FROM join_form WHERE user_id = :id");
            $stmt->execute([':id' => $id]);

            // Xóa tài khoản
            $stmt = $this->conn->prepare("DELETE FROM account WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Lỗi khi xóa tài khoản: " . $e->getMessage());
            return false;
        }
    }


    // Lấy tất cả tài khoản (cập nhật để lấy thêm status)
    public function getAllAccounts()
    {
        $query = "SELECT id, username, fullname, role, email, phone, avatar, 
                     status, disable_reason, disabled_at,
                     created_at, updated_at 
              FROM " . $this->table_name . " 
              ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy tài khoản theo ID
    // public function getAccountById($id)
    // {
    //     $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
    //     $stmt = $this->conn->prepare($query);
    //     $stmt->bindParam(":id", $id);
    //     $stmt->execute();
    //     return $stmt->fetch(PDO::FETCH_OBJ);
    // }

    // Cập nhật mật khẩu
    public function updatePassword($email, $password)
    {
        $query = "UPDATE " . $this->table_name . " 
                 SET password = :password, 
                     updated_at = NOW()
                 WHERE email = :email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':email', $email);

        return $stmt->execute();
    }

    // Lấy danh sách tài khoản ngắn
    public function getAccounts()
    {
        $query = "SELECT id, username FROM " . $this->table_name . " ORDER BY username";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }


    public function getUserById($id)
    {
        $stmt = $this->conn->prepare("SELECT avatar, fullname FROM account WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['avatar' => '/uploads/default_avatar.jpg', 'fullname' => 'Unknown'];
    }

    public function getAccountById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Sử dụng FETCH_ASSOC thay vì FETCH_OBJ để nhất quán với các model khác
    }
    public function getNotifications($user_id)
    {
        $stmt = $this->conn->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUnreadNotificationCount($user_id)
    {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = :user_id AND is_read = 0");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function markAllNotificationsAsRead($user_id)
    {
        $stmt = $this->conn->prepare("UPDATE notifications SET 'read' = 1 WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteNotification($notification_id, $user_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM notifications WHERE id = :notification_id AND user_id = :user_id");
        $stmt->bindParam(':notification_id', $notification_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function deleteAllNotifications($user_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM notifications WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Vô hiệu hóa tài khoản
    public function disableAccount($id, $reason)
    {
        try {
            $query = "UPDATE " . $this->table_name . " 
                  SET status = 'disabled', 
                      disable_reason = :reason,
                      disabled_at = NOW(),
                      updated_at = NOW()
                  WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':reason', $reason);
            $stmt->bindParam(':id', $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Disable Account Error: " . $e->getMessage());
            return false;
        }
    }

    // Kích hoạt lại tài khoản
    public function enableAccount($id)
    {
        try {
            $query = "UPDATE " . $this->table_name . " 
                  SET status = 'active', 
                      disable_reason = NULL,
                      disabled_at = NULL,
                      updated_at = NOW()
                  WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Enable Account Error: " . $e->getMessage());
            return false;
        }
    }
    // Tạo yêu cầu mở lại tài khoản
    public function createReactivationRequest($user_id, $reason)
    {
        try {
            // Tạo thông báo cho admin
            $query = "INSERT INTO notifications (user_id, title, message, link, created_at)
                  SELECT id, 'Yêu cầu mở lại tài khoản', 
                         CONCAT('Người dùng ', (SELECT username FROM account WHERE id = ?), ' đã gửi yêu cầu mở lại tài khoản. Lý do: ', ?),
                         CONCAT('/webdacn_quanlyclb/account/manage/', ?),
                         NOW()
                  FROM account 
                  WHERE role = 'admin'";

            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$user_id, $reason, $user_id]);
        } catch (PDOException $e) {
            error_log("Create Reactivation Request Error: " . $e->getMessage());
            return false;
        }
    }

    // Kiểm tra tài khoản có bị vô hiệu hóa không
    public function isAccountDisabled($username)
    {
        $query = "SELECT status, disable_reason FROM " . $this->table_name . " 
              WHERE username = :username AND status = 'disabled'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách câu lạc bộ mà user đã tham gia
    public function getUserTeams($user_id)
    {
        try {
            $query = "SELECT t.id, t.name, t.description, t.avatar_team, 
                         ut.joined_at, ut.point
                  FROM team t
                  INNER JOIN user_team ut ON t.id = ut.team_id
                  WHERE ut.user_id = ?
                  ORDER BY ut.joined_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get User Teams Error: " . $e->getMessage());
            return [];
        }
    }

    // Lấy danh sách câu lạc bộ mà user là chủ nhiệm
    public function getUserLeaderTeams($user_id)
    {
        try {
            $query = "SELECT id, name, description, avatar_team, created_at
                  FROM team
                  WHERE user_id = ?
                  ORDER BY created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute([$user_id]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get User Leader Teams Error: " . $e->getMessage());
            return [];
        }
    }

    public function updateReactivationRequestStatus($request_id, $status)
    {
        try {
            $query = "UPDATE reactivation_requests 
                      SET status = :status, 
                          updated_at = NOW()
                      WHERE id = :request_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update Reactivation Request Status Error: " . $e->getMessage());
            return false;
        }
    }

    public function getReactivationRequests($user_id)
    {
        try {
            $query = "SELECT id, reason, status, created_at, updated_at
                      FROM reactivation_requests
                      WHERE user_id = :user_id
                      ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Reactivation Requests Error: " . $e->getMessage());
            return [];
        }
    }

    public function getAccountByEmail($email)
    {
        $query = "SELECT * FROM account WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function saveSocial($username, $fullname, $password, $role, $email, $phone, $avatar, $provider, $providerId)
    {
        $query = "INSERT INTO account 
              (username, fullname, password, role, email, phone, avatar, provider, provider_id, created_at)
              VALUES 
              (:username, :fullname, :password, :role, :email, :phone, :avatar, :provider, :provider_id, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':avatar', $avatar);
        $stmt->bindParam(':provider', $provider);
        $stmt->bindParam(':provider_id', $providerId);
        return $stmt->execute();
    }
}
