<?php
require_once __DIR__ . '/../config/Database.php';

class AdvertisingBannerModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        if ($this->db === null) {
            throw new Exception("Không thể kết nối CSDL!");
        }
    }

    /**
     * Lấy tất cả banner đang hoạt động để hiển thị
     */
    public function getAllActiveBanners()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM advertising_banners WHERE is_active = 1 ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy banner: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy TẤT CẢ banner cho trang Admin
     */
    public function getAllBannersForAdmin()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM advertising_banners ORDER BY created_at DESC");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy banner (admin): " . $e->getMessage());
            return [];
        }
    }

    /**
     * Thêm banner mới
     */
    public function addBanner($image_path, $alt_text, $link_url)
    {
        try {
            $sql = "INSERT INTO advertising_banners (image_path, alt_text, link_url) 
                    VALUES (:image_path, :alt_text, :link_url)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':image_path' => $image_path,
                ':alt_text' => $alt_text,
                ':link_url' => $link_url ?: null // Lưu NULL nếu link rỗng
            ]);
        } catch (PDOException $e) {
            error_log("Lỗi khi thêm banner: " . $e->getMessage());
            return false;
        }
    }
    public function updateBanner($id, $image_path, $alt_text, $link_url)
    {
        try {
            $sql = "UPDATE advertising_banners 
                SET image_path = :image_path, alt_text = :alt_text, link_url = :link_url 
                WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id' => $id,
                ':image_path' => $image_path,
                ':alt_text' => $alt_text,
                ':link_url' => $link_url ?: null
            ]);
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật banner: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy 1 banner bằng ID (để xóa)
     */
    public function getBannerById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM advertising_banners WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi khi lấy banner ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Xóa 1 banner
     */
    public function deleteBanner($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM advertising_banners WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Lỗi khi xóa banner: " . $e->getMessage());
            return false;
        }
    }

    // Bật tắt Banner
    public function toggleActive($id, $is_active)
    {
        try {
            $sql = "UPDATE advertising_banners SET is_active = :is_active WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([':id' => $id, ':is_active' => $is_active]);
        } catch (PDOException $e) {
            error_log("Lỗi toggle banner: " . $e->getMessage());
            return false;
        }
    }
}
