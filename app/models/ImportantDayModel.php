<?php
class ImportantDayModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        if ($this->db === null) {
            throw new Exception("Không thể kết nối tới cơ sở dữ liệu!");
        }
    }

    public function getDaysByUser($user_id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM important_days WHERE user_id = :user_id ORDER BY date DESC");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error fetching days: " . $e->getMessage());
            throw new Exception("Lỗi khi lấy danh sách ngày quan trọng: " . $e->getMessage());
        }
    }

    public function addDay($user_id, $title, $date, $description) {
        try {
            if (empty($title) || empty($date)) {
                throw new Exception("Tiêu đề và ngày không được để trống!");
            }
            if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
                throw new Exception("Định dạng ngày không hợp lệ!");
            }
            $stmt = $this->db->prepare("INSERT INTO important_days (user_id, title, date, description) VALUES (:user_id, :title, :date, :description)");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error adding day: " . $e->getMessage());
            throw new Exception("Lỗi khi thêm ngày quan trọng: " . $e->getMessage());
        }
    }

    public function getDayById($id) {
        try {
            $stmt = $this->db->prepare("SELECT id.* FROM important_days id JOIN account a ON id.user_id = a.id WHERE id.id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error fetching day: " . $e->getMessage());
            throw new Exception("Lỗi khi lấy thông tin ngày quan trọng: " . $e->getMessage());
        }
    }

    public function updateDay($id, $title, $date, $description) {
        try {
            if (empty($title) || empty($date)) {
                throw new Exception("Tiêu đề và ngày không được để trống!");
            }
            if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) {
                throw new Exception("Định dạng ngày không hợp lệ!");
            }
            $stmt = $this->db->prepare("UPDATE important_days SET title = :title, date = :date, description = :description WHERE id = :id");
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating day: " . $e->getMessage());
            throw new Exception("Lỗi khi cập nhật ngày quan trọng: " . $e->getMessage());
        }
    }

    public function deleteDay($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM important_days WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error deleting day: " . $e->getMessage());
            throw new Exception("Lỗi khi xóa ngày quan trọng: " . $e->getMessage());
        }
    }
}
?>