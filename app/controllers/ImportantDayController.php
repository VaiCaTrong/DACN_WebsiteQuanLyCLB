<?php
require_once 'app/config/database.php';
require_once 'app/models/ImportantDayModel.php';
require_once 'app/helpers/SessionHelper.php';

class ImportantDayController {
    private $dayModel;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        if ($this->db === null) {
            $_SESSION['error'] = "Không thể kết nối tới cơ sở dữ liệu!";
            header('Location: /webdacn_quanlyclb');
            exit;
        }
        $this->dayModel = new ImportantDayModel($this->db);
    }

    public function index() {
        SessionHelper::requireLogin();
        $user_id = $this->getUserId();
        try {
            $days = $this->dayModel->getDaysByUser($user_id);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $days = [];
        }
        include 'app/views/important_day/index.php';
    }

    public function add() {
        SessionHelper::requireLogin();
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $title = trim($_POST['title'] ?? '');
            $date = trim($_POST['date'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $user_id = $this->getUserId();

            if (empty($title)) $errors['title'] = "Vui lòng nhập tiêu đề!";
            if (empty($date)) $errors['date'] = "Vui lòng chọn ngày!";
            elseif (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $date)) $errors['date'] = "Định dạng ngày không hợp lệ!";

            if (empty($errors)) {
                try {
                    if ($this->dayModel->addDay($user_id, $title, $date, $description  )) {
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
        include 'app/views/important_day/add.php';
    }

    public function edit($id) {
        SessionHelper::requireLogin();
        try {
            $day = $this->dayModel->getDayById($id);
            if ($day && $day->user_id == $this->getUserId()) {
                include 'app/views/important_day/edit.php';
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
                    if ($day && $day->user_id == $this->getUserId()) {
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
            include 'app/views/important_day/edit.php';
        } else {
            header('Location: /webdacn_quanlyclb/ImportantDay');
        }
    }

    public function delete($id) {
        SessionHelper::requireLogin();
        try {
            $day = $this->dayModel->getDayById($id);
            if ($day && $day->user_id == $this->getUserId()) {
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

    private function getUserId() {
        try {
            $stmt = $this->db->prepare("SELECT id FROM account WHERE username = :username");
            $stmt->bindParam(':username', $_SESSION['username'], PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            if ($result) {
                return $result->id;
            } else {
                throw new Exception("Không tìm thấy người dùng!");
            }
        } catch (PDOException $e) {
            error_log("Error fetching user ID: " . $e->getMessage());
            throw new Exception("Lỗi khi lấy ID người dùng: " . $e->getMessage());
        }
    }
}
?>