<?php
require_once __DIR__ . '/../config/Database.php';

class EventModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * Thêm một sự kiện mới (ĐÃ CÓ category)
     */
    public function addEvent($team_id, $user_id, $category, $title, $description, $event_date, $location, $image_path)
    {
        $sql = "INSERT INTO event (team_id, user_id, category, title, description, event_date, location, image_path, created_at)
                VALUES (:team_id, :user_id, :category, :title, :description, :event_date, :location, :image_path, NOW())";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':team_id' => $team_id,
            ':user_id' => $user_id,
            ':category' => $category, // <-- THÊM MỚI
            ':title' => $title,
            ':description' => $description,
            ':event_date' => $event_date,
            ':location' => $location,
            ':image_path' => $image_path
        ]);
    }

    // /**
    //  * Lấy sự kiện mới nhất để hiển thị
    //  */
    // public function getLatestEvent()
    // {
    //     // Ưu tiên 1: Lấy sự kiện SẮP DIỄN RA gần nhất (ngày diễn ra >= hôm nay)
    //     $stmt = $this->db->prepare("
    //         SELECT e.*, t.name as team_name
    //         FROM event e
    //         LEFT JOIN team t ON e.team_id = t.id
    //         WHERE e.event_date >= CURDATE() 
    //         ORDER BY e.event_date ASC 
    //         LIMIT 1
    //     ");
    //     $stmt->execute();
    //     $event = $stmt->fetch(PDO::FETCH_ASSOC);

    //     if ($event) {
    //         return $event;
    //     }

    //     // Ưu tiên 2: Nếu không có, lấy sự kiện VỪA MỚI TẠO gần nhất
    //     $stmt = $this->db->prepare("
    //         SELECT e.*, t.name as team_name
    //         FROM event e
    //         LEFT JOIN team t ON e.team_id = t.id
    //         ORDER BY e.created_at DESC 
    //         LIMIT 1
    //     ");
    //     $stmt->execute();
    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }
    
    /**
     * === HÀM MỚI: Lấy chi tiết 1 sự kiện ===
     */
    public function getEventById($id)
    {
        $stmt = $this->db->prepare("
            SELECT e.*, t.name as team_name, a.fullname as author_name
            FROM event e
            LEFT JOIN team t ON e.team_id = t.id
            LEFT JOIN account a ON e.user_id = a.id
            WHERE e.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * === HÀM MỚI: Xóa 1 sự kiện ===
     */
    public function deleteEvent($id)
    {
        $stmt = $this->db->prepare("DELETE FROM event WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * === HÀM MỚI: Kiểm tra xem user đã tham gia sự kiện chưa ===
     */
    public function checkUserParticipation($event_id, $user_id)
    {
        $sql = "SELECT COUNT(*) FROM event_participants WHERE event_id = :event_id AND user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':event_id' => $event_id, ':user_id' => $user_id]);
        return $stmt->fetchColumn() > 0; // Trả về true nếu đã tham gia, false nếu chưa
    }

    /**
     * === HÀM MỚI: Đăng ký tham gia sự kiện cho user ===
     */
    public function joinEvent($event_id, $user_id)
    {
        // Kiểm tra lại lần nữa để tránh lỗi (dù đã có UNIQUE KEY)
        if ($this->checkUserParticipation($event_id, $user_id)) {
            return false; // Hoặc ném Exception nếu muốn
        }

        $sql = "INSERT INTO event_participants (event_id, user_id, status, registered_at)
                VALUES (:event_id, :user_id, 'registered', NOW())";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':event_id' => $event_id, ':user_id' => $user_id]);
    }

    /**
     * Toggle trạng thái sự kiện
     */
    public function toggleActive($id, $is_active)
    {
        try {
            $sql = "UPDATE event SET is_active = :is_active WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id, ':is_active' => $is_active]);
        } catch (PDOException $e) {
            error_log("Lỗi toggle event: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tất cả sự kiện cho admin (bao gồm is_active)
     */
    public function getAllEventsForAdmin()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM event ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi lấy events: " . $e->getMessage());
            return [];
        }
    }

    public function getActiveEvents($limit = 3)
    {
        $stmt = $this->db->prepare("
        SELECT e.*, t.name as team_name
        FROM event e
        LEFT JOIN team t ON e.team_id = t.id
        WHERE e.is_active = 1
        ORDER BY e.created_at DESC
        LIMIT :limit
    ");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
