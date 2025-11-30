<?php
require_once __DIR__ . '/../models/AdvertisingBannerModel.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

class AdvertisingBannerController
{
    private $bannerModel;

    public function __construct()
    {
        // Yêu cầu Admin cho tất cả các hàm trong Controller này
        if (!SessionHelper::isAdmin()) {
            $_SESSION['error'] = "Bạn không có quyền truy cập khu vực này.";
            header("Location: /webdacn_quanlyclb");
            exit();
        }
        $this->bannerModel = new AdvertisingBannerModel();
    }

    /**
     * Hiển thị trang quản lý (danh sách banner)
     */
    public function index()
    {
        $banners = $this->bannerModel->getAllBannersForAdmin();
        include 'app/views/advertising_banner/index.php'; // Sẽ tạo view này
    }

    /**
     * Xử lý lưu banner mới (từ form)
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /webdacn_quanlyclb/advertisingbanner");
            exit();
        }

        $alt_text = $_POST['alt_text'] ?? 'Ảnh quảng cáo';
        $link_url = filter_var($_POST['link_url'] ?? '', FILTER_SANITIZE_URL);
        $image_path = null;

        // Xử lý Upload Ảnh (Giữ nguyên chất lượng)
        if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/public/uploads/banners/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

            $tmpName = $_FILES['banner_image']['tmp_name'];
            $originalName = basename($_FILES['banner_image']['name']);
            $fileExtension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $newFileName = uniqid('banner_') . '.' . $fileExtension;
            $targetPath = $uploadDir . $newFileName;

            // Kiểm tra cơ bản
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($fileExtension, $allowedTypes)) {
                $_SESSION['error'] = "Chỉ chấp nhận file ảnh (JPG, PNG, GIF, WEBP).";
                header("Location: /webdacn_quanlyclb/advertisingbanner");
                exit();
            }
            if ($_FILES['banner_image']['size'] > 10 * 1024 * 1024) { // 10MB
                $_SESSION['error'] = "Kích thước ảnh không được vượt quá 10MB.";
                header("Location: /webdacn_quanlyclb/advertisingbanner");
                exit();
            }

            // Di chuyển file (Không resize, giữ nguyên chất lượng)
            if (move_uploaded_file($tmpName, $targetPath)) {
                $image_path = 'public/uploads/banners/' . $newFileName; // Đường dẫn tương đối
            } else {
                $_SESSION['error'] = "Lỗi khi di chuyển file ảnh.";
                header("Location: /webdacn_quanlyclb/advertisingbanner");
                exit();
            }
        } else {
            $_SESSION['error'] = "Vui lòng chọn một file ảnh.";
            header("Location: /webdacn_quanlyclb/advertisingbanner");
            exit();
        }

        // Lưu vào CSDL
        if ($image_path && $this->bannerModel->addBanner($image_path, $alt_text, $link_url)) {
            $_SESSION['message'] = "Thêm ảnh quảng cáo thành công!";
        } else {
            if (isset($targetPath) && file_exists($targetPath)) {
                unlink($targetPath); // Xóa ảnh đã lỡ upload nếu lỗi DB
            }
            $_SESSION['error'] = "Lỗi khi thêm ảnh quảng cáo vào CSDL.";
        }
        header("Location: /webdacn_quanlyclb/advertisingbanner");
        exit();
    }
    public function edit($id)
    {
        $banner = $this->bannerModel->getBannerById($id);
        if (!$banner) {
            $_SESSION['error'] = "Không tìm thấy banner.";
            header("Location: /webdacn_quanlyclb/advertisingbanner");
            exit();
        }
        include 'app/views/advertising_banner/edit.php';
    }

    public function update($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /webdacn_quanlyclb/advertisingbanner");
            exit();
        }

        $banner = $this->bannerModel->getBannerById($id);
        if (!$banner) {
            $_SESSION['error'] = "Banner không tồn tại.";
            header("Location: /webdacn_quanlyclb/advertisingbanner");
            exit();
        }

        $alt_text = $_POST['alt_text'] ?? $banner['alt_text'];
        $link_url = filter_var($_POST['link_url'] ?? '', FILTER_SANITIZE_URL);
        $image_path = $banner['image_path']; // giữ nguyên

        // Xử lý ảnh mới (nếu có)
        if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/public/uploads/banners/';
            $fileExtension = strtolower(pathinfo($_FILES['banner_image']['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid('banner_') . '.' . $fileExtension;
            $targetPath = $uploadDir . $newFileName;

            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (!in_array($fileExtension, $allowed)) {
                $_SESSION['error'] = "Chỉ chấp nhận ảnh JPG, PNG, GIF, WEBP.";
                header("Location: /webdacn_quanlyclb/advertisingbanner/edit/$id");
                exit();
            }

            if (move_uploaded_file($_FILES['banner_image']['tmp_name'], $targetPath)) {
                // Xóa ảnh cũ
                $oldPath = $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/' . $banner['image_path'];
                if (file_exists($oldPath)) unlink($oldPath);

                $image_path = 'public/uploads/banners/' . $newFileName;
            }
        }

        if ($this->bannerModel->updateBanner($id, $image_path, $alt_text, $link_url)) {
            $_SESSION['message'] = "Cập nhật thành công!";
        } else {
            $_SESSION['error'] = "Lỗi khi cập nhật.";
        }
        header("Location: /webdacn_quanlyclb/advertisingbanner");
        exit();
    }

    /**
     * Xử lý xóa banner
     */
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Chỉ cho phép xóa bằng POST
            $_SESSION['error'] = "Yêu cầu không hợp lệ.";
            header("Location: /webdacn_quanlyclb/advertisingbanner");
            exit();
        }

        $banner = $this->bannerModel->getBannerById($id);
        if ($banner) {
            // 1. Xóa file ảnh
            $filePath = $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/' . $banner['image_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            // 2. Xóa trong CSDL
            $this->bannerModel->deleteBanner($id);
            $_SESSION['message'] = "Xóa ảnh quảng cáo thành công!";
        } else {
            $_SESSION['error'] = "Không tìm thấy ảnh quảng cáo để xóa.";
        }
        header("Location: /webdacn_quanlyclb/advertisingbanner");
        exit();
    }
    /**
     * Hiển thị form tạo banner mới
     */
    public function create()
    {
        // Chỉ cần include form
        include 'app/views/advertising_banner/create.php';
    }

    // AdvertisingBannerController.php
    public function toggleAjax()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !SessionHelper::isAdmin()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Không có quyền']);
            exit();
        }

        $id = $_POST['id'] ?? null;
        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
            exit();
        }

        $banner = $this->bannerModel->getBannerById($id);
        if (!$banner) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy banner']);
            exit();
        }

        $newStatus = $banner['is_active'] ? 0 : 1;
        if ($this->bannerModel->toggleActive($id, $newStatus)) {
            echo json_encode([
                'success' => true,
                'new_status' => $newStatus,
                'message' => $newStatus ? 'Đã bật banner' : 'Đã tắt banner'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật']);
        }
        exit();
    }
}
