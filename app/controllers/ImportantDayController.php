<?php
require_once 'app/config/database.php';
require_once 'app/models/ImportantDayModel.php';
require_once 'app/helpers/SessionHelper.php';

class ImportantDayController {
    private $dayModel;
    private $db;

    public function __construct() {
        if ($this->db === null) {
            $_SESSION['error'] = "Không thể kết nối tới cơ sở dữ liệu!";
            header('Location: /webdacn_quanlyclb');
            exit;
        }
        $this->dayModel = new ImportantDayModel($this->db);
    }

    public function index() {
        SessionHelper::requireLogin();
        // === SỬA LỖI: DÙNG SESSIONHELPER ===
        $user_id = SessionHelper::getUserId(); 
        // === KẾT THÚC SỬA LỖI ===
        try {
            $days = $this->dayModel->getDaysByUser($user_id);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $days = [];
        }
        // === SỬA LỖI: THÊM HEADER VÀ FOOTER ===
        include 'app/views/shares/header.php';
        include 'app/views/important_day/index.php';
        include 'app/views/shares/footer.php';
        // === KẾT THÚC SỬA LỖI ===
    }

    public function add() {
        SessionHelper::requireLogin();
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $title = trim($_POST['title'] ?? '');
            $date = trim($_POST['date'] ?? '');
            $description = trim($_POST['description'] ?? '');
            // === SỬA LỖI: DÙNG SESSIONHELPER ===
            $user_id = SessionHelper::getUserId();
            // === KẾT THÚC SỬA LỖI ===

            if (empty($title)) $errors['title'] = "Vui lòng nhập tiêu đề!";
            if (empty($date)) $errors['date'] = "Vui lòng chọn ngày!";
            elseif (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) $errors['date'] = "Định dạng ngày không hợp lệ!";

            if (empty($errors)) {
                try {
                    // === SỬA LỖI: Sửa thứ tự tham số nếu model yêu cầu ===
                    // Giả sử model là: user_id, title, date, description
                    if ($this->dayModel->addDay($user_id, $title, $date, $description)) { 
                    // === KẾT THÚC SỬA LỖI ===
                        $_SESSION['message'] = "Thêm ngày quan trọng thành công!";
                        header('Location: /webdacn_quanlyclb/ImportantDay');
                        exit;
                    } else {
                        $errors['general'] = "Không thể thêm ngày quan trọng!";
                    }
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
        }
        // === SỬA LỖI: THÊM HEADER VÀ FOOTER ===
        include 'app/views/important_day/add.php';
         // === KẾT THÚC SỬA LỖI ===
    }

    public function edit($id) {
        SessionHelper::requireLogin();
        // === SỬA LỖI: DÙNG SESSIONHELPER ===
        $current_user_id = SessionHelper::getUserId();
        // === KẾT THÚC SỬA LỖI ===
        try {
            $day = $this->dayModel->getDayById($id);
            // Sửa kiểm tra quyền
            if ($day && $day->user_id == $current_user_id) { 
                // === SỬA LỖI: THÊM HEADER VÀ FOOTER ===
                include 'app/views/important_day/edit.php';
                // === KẾT THÚC SỬA LỖI ===
            } else {
                $_SESSION['error'] = "Không tìm thấy ngày quan trọng hoặc bạn không có quyền!";
                header('Location: /webdacn_quanlyclb/ImportantDay');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header('Location: /webdacn_quanlyclb/ImportantDay');
            exit;
        }
    }

    public function update() {
        SessionHelper::requireLogin();
        // === SỬA LỖI: DÙNG SESSIONHELPER ===
        $current_user_id = SessionHelper::getUserId();
        // === KẾT THÚC SỬA LỖI ===
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'] ?? '';
            $title = trim($_POST['title'] ?? '');
            $date = trim($_POST['date'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $errors = [];

            if (empty($title)) $errors['title'] = "Vui lòng nhập tiêu đề!";
            if (empty($date)) $errors['date'] = "Vui lòng chọn ngày!";
            elseif (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) $errors['date'] = "Định dạng ngày không hợp lệ!";

            if (empty($errors)) {
                try {
                    $day = $this->dayModel->getDayById($id);
                    // Sửa kiểm tra quyền
                    if ($day && $day->user_id == $current_user_id) { 
                        if ($this->dayModel->updateDay($id, $title, $date, $description)) {
                            $_SESSION['message'] = "Cập nhật ngày quan trọng thành công!";
                            header('Location: /webdacn_quanlyclb/ImportantDay');
                            exit;
                        } else {
                            $errors['general'] = "Không thể cập nhật ngày quan trọng!";
                        }
                    } else {
                        $_SESSION['error'] = "Không tìm thấy ngày quan trọng hoặc bạn không có quyền!";
                        header('Location: /webdacn_quanlyclb/ImportantDay');
                        exit;
                    }
                } catch (Exception $e) {
                    $errors['general'] = $e->getMessage();
                }
            }
            // === SỬA LỖI: THÊM HEADER VÀ FOOTER KHI CÓ LỖI ===
            include 'app/views/shares/header.php';
            // Cần lấy lại $day để truyền vào view edit khi có lỗi
            $day = (object) $_POST; // Lấy lại dữ liệu form để hiển thị lại
            include 'app/views/important_day/edit.php'; 
            include 'app/views/shares/footer.php';
             // === KẾT THÚC SỬA LỖI ===
        } else {
            header('Location: /webdacn_quanlyclb/ImportantDay');
        }
    }

    public function delete($id) {
        SessionHelper::requireLogin();
        // === SỬA LỖI: DÙNG SESSIONHELPER ===
        $current_user_id = SessionHelper::getUserId();
        // === KẾT THÚC SỬA LỖI ===
        try {
            $day = $this->dayModel->getDayById($id);
            // Sửa kiểm tra quyền
            if ($day && $day->user_id == $current_user_id) { 
                if ($this->dayModel->deleteDay($id)) {
                    $_SESSION['message'] = "Xóa ngày quan trọng thành công!";
                } else {
                    $_SESSION['error'] = "Không thể xóa ngày quan trọng!";
                }
            } else {
                $_SESSION['error'] = "Không tìm thấy ngày quan trọng hoặc bạn không có quyền!";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        header('Location: /webdacn_quanlyclb/ImportantDay');
        exit;
    }
}
?>