<?php
class SessionHelper
{
    /**
     * Khởi tạo session nếu chưa được bắt đầu
     */
    public static function start()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Đặt một biến session
     * @param string $key Khóa của session
     * @param mixed $value Giá trị của session
     */
    public static function set($key, $value)
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Kiểm tra xem người dùng đã đăng nhập hay chưa
     * @return bool
     */
    public static function isLoggedIn()
    {
        self::start();
        return isset($_SESSION['user_id']) && isset($_SESSION['username']);
    }

    /**
     * Kiểm tra xem người dùng có phải là admin hay không
     * @return bool
     */
    public static function isAdmin()
    {
        self::start();
        return self::isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    //Kiểm tra là User
    public static function isUser()
    {
        self::start();
        return self::isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'user';
    }
    //Kiểm tra là Staff
    public static function isStaff()
    {
        self::start();
        return self::isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'staff';
    }

    /**
     * Kiểm tra xem người dùng có phải là leader của một team cụ thể hay không
     * @param int $teamId ID của team
     * @param object $db Kết nối cơ sở dữ liệu PDO
     * @return bool
     */
    public static function isTeamLeader($teamId, $db)
    {
        self::start();
        if (!self::isLoggedIn()) {
            return false;
        }

        $userId = self::getUserId();
        $query = "SELECT user_id FROM Team WHERE id = :teamId";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':teamId', $teamId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result && $result['user_id'] == $userId;
    }

    /**
     * Kiểm tra xem người dùng có vai trò cụ thể hay không
     * @param string $role Vai trò cần kiểm tra (admin, leader, member)
     * @return bool
     */
    public static function hasRole($role)
    {
        self::start();
        return self::isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === $role;
    }

    /**
     * Lấy vai trò của người dùng
     * @return string Vai trò (admin, leader, member, hoặc guest)
     */
    public static function getRole()
    {
        self::start();
        return isset($_SESSION['role']) ? $_SESSION['role'] : 'guest';
    }



    /**
     * Lấy ID của người dùng hiện tại
     * @return int|null
     */
    public static function getUserId()
    {
        self::start();
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    /**
     * Yêu cầu đăng nhập, chuyển hướng nếu chưa đăng nhập
     * @param string $redirectUrl URL để chuyển hướng sau khi đăng nhập
     */
    public static function requireLogin($redirectUrl = null)
    {
        if (!self::isLoggedIn()) {
            $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
            // Dùng hằng số BASE_URL
            $loginUrl = defined('BASE_URL') ? BASE_URL . '/account/login' : '/webdacn_quanlyclb/account/login';
            header("Location: " . ($redirectUrl ?? $loginUrl));
            exit;
        }
    }

    /**
     * Kiểm tra quyền truy cập cho hành động quản lý team
     * Admin có toàn quyền, leader chỉ có quyền trên team của mình
     * @param int $teamId ID của team
     * @param object $db Kết nối cơ sở dữ liệu PDO
     * @return bool
     */
    public static function canManageTeam($teamId, $db)
    {
        return self::isAdmin() || self::isTeamLeader($teamId, $db);
    }

    /**
     * Đăng xuất người dùng
     */
    public static function logout()
    {
        self::start();
        session_unset();
        session_destroy();
        // Dùng hằng số BASE_URL
        $homeUrl = defined('BASE_URL') ? BASE_URL : '/webdacn_quanlyclb/';
        header('Location: ' . $homeUrl);
        exit;
    }
    public static function getCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verifyCsrfToken($token)
    {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    // Thêm vào class SessionHelper trong SessionHelper.php

    /**
     * Kiểm tra xem staff có quản lý CLB nào không
     * @param object $db Kết nối cơ sở dữ liệu PDO
     * @return array Danh sách CLB mà staff quản lý
     */
    public static function getManagedClubs($db)
    {
        self::start();
        if (!self::isStaff()) {
            return [];
        }

        $userId = self::getUserId();
        $query = "SELECT t.id, t.name FROM team t WHERE t.user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kiểm tra xem staff có quản lý CLB cụ thể không
     * @param int $teamId ID của CLB
     * @param object $db Kết nối cơ sở dữ liệu PDO
     * @return bool
     */
    public static function isClubManager($teamId, $db)
    {
        self::start();
        if (!self::isStaff()) {
            return false;
        }

        $userId = self::getUserId();
        $query = "SELECT id FROM team WHERE id = :team_id AND user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':team_id', $teamId, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch() !== false;
    }
}
