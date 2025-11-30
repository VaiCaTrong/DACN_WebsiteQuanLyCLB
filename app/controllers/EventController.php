<?php
require_once __DIR__ . '/../models/EventModel.php';
require_once __DIR__ . '/../models/TeamModel.php';
require_once __DIR__ . '/../helpers/SessionHelper.php';

class EventController
{
    private $eventModel;
    private $teamModel;
    private $importantDayModel;

    public function __construct()
    {
        $this->eventModel = new EventModel();
        $this->teamModel = new TeamModel();    // Cần truyền kết nối DB vào
    }

    // Hàm kiểm tra quyền Admin/Staff
    private function requireStaffOrAdmin()
    {
        if (!SessionHelper::isLoggedIn() || !in_array(SessionHelper::getRole(), ['admin', 'staff'])) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện hành động này!";
            header("Location: /webdacn_quanlyclb");
            exit();
        }
    }
    // Hàm kiểm tra CHỈ ADMIN
    private function requireAdmin()
    {
        if (!SessionHelper::isAdmin()) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện hành động này!";
            header("Location: /webdacn_quanlyclb");
            exit();
        }
    }
    /**
     * Hiển thị danh sách quản lý sự kiện (index)
     */
    public function index()
    {
        $this->requireAdmin();  // Chỉ admin
        $events = $this->eventModel->getAllEventsForAdmin();
        include 'app/views/event/index.php';  // View mới
    }

    /**
     * Hiển thị form tạo sự kiện
     */
    public function create()
    {
        $this->requireStaffOrAdmin();

        $user_id = SessionHelper::getUserId();
        $role = SessionHelper::getRole();

        // Lấy danh sách CLB mà user này có thể đăng bài
        $teams = $this->teamModel->getTeamsForUser($user_id, $role);

        if (empty($teams)) {
            $_SESSION['error'] = "Bạn không quản lý CLB nào để tạo sự kiện!";
            header("Location: /webdacn_quanlyclb");
            exit();
        }

        include 'app/views/shares/header.php';
        include 'app/views/event/create.php'; // Sẽ tạo file này ở bước 4
        include 'app/views/shares/footer.php';
    }

    /**
     * Xử lý lưu sự kiện mới (ĐÃ CÓ category)
     */
    public function store()
    {
        $this->requireStaffOrAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /webdacn_quanlyclb/event/create");
            exit();
        }

        // Lấy dữ liệu
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $event_date = $_POST['event_date'] ?? '';
        $location = $_POST['location'] ?? '';
        $category = $_POST['category'] ?? null; // Lấy category
        $team_id = $_POST['team_id'] ?? null; // Lấy team_id (nếu có)
        $user_id = SessionHelper::getUserId();

        // === LOGIC MỚI: XỬ LÝ TEAM_ID DỰA TRÊN CATEGORY ===
        if (empty($category)) {
            $_SESSION['error'] = "Vui lòng chọn loại sự kiện.";
            header("Location: /webdacn_quanlyclb/event/create");
            exit();
        }

        if ($category === 'clb') {
            // Nếu là sự kiện CLB, BẮT BUỘC phải có team_id
            if (empty($team_id)) {
                $_SESSION['error'] = "Vui lòng chọn CLB tổ chức.";
                header("Location: /webdacn_quanlyclb/event/create");
                exit();
            }
        } else {
            // Nếu là sự kiện Trường hoặc Nhà tài trợ, BẮT BUỘC team_id = NULL
            $team_id = null;
        }
        // === KẾT THÚC LOGIC MỚI ===

        // --- Xử lý Upload Ảnh (Giữ nguyên) ---
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/public/uploads/events/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $fileName = uniqid() . '-' . basename($_FILES['image']['name']);
            $targetFile = $upload_dir . $fileName;

            $imageInfo = getimagesize($_FILES['image']['tmp_name']);
            if ($imageInfo === false) {
                $_SESSION['error'] = "File tải lên không phải là ảnh.";
                header("Location: /webdacn_quanlyclb/event/create");
                exit();
            }

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $image_path = 'public/uploads/events/' . $fileName;
            } else {
                $_SESSION['error'] = "Lỗi khi tải ảnh lên.";
                header("Location: /webdacn_quanlyclb/event/create");
                exit();
            }
        } else {
            $_SESSION['error'] = "Vui lòng cung cấp ảnh cho sự kiện.";
            header("Location: /webdacn_quanlyclb/event/create");
            exit();
        }

        // Lưu vào DB
        try {
            $this->eventModel->addEvent($team_id, $user_id, $category, $title, $description, $event_date, $location, $image_path);
            $_SESSION['message'] = "Tạo sự kiện thành công!";
            header("Location: /webdacn_quanlyclb");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi khi tạo sự kiện: " . $e->getMessage();
            header("Location: /webdacn_quanlyclb/event/create");
            exit();
        }
    }

    /**
     * === HÀM MỚI: Hiển thị chi tiết sự kiện ===
     */
    public function detail($id)
    {
        $event = $this->eventModel->getEventById($id);

        if (!$event) {
            $_SESSION['error'] = "Không tìm thấy sự kiện này.";
            header("Location: /webdacn_quanlyclb");
            exit();
        }

        $canDelete = SessionHelper::isAdmin();

        // === THÊM CODE MỚI: KIỂM TRA THAM GIA ===
        $hasJoined = false; // Mặc định là chưa tham gia
        $canJoin = false; // Mặc định là không thể tham gia (vd: admin, chưa login)

        if (SessionHelper::isLoggedIn() && !SessionHelper::isAdmin()) {
            $currentUserId = SessionHelper::getUserId();
            $canJoin = true; // User thường có thể tham gia
            $hasJoined = $this->eventModel->checkUserParticipation($id, $currentUserId);
        }
        // === KẾT THÚC CODE MỚI ===

        include 'app/views/shares/header.php';
        include 'app/views/event/detail.php'; // Các biến $hasJoined, $canJoin sẽ có sẵn
        include 'app/views/shares/footer.php';
    }

    /**
     * === HÀM MỚI: Xử lý xóa sự kiện (Chỉ Admin) ===
     */
    public function delete($id)
    {
        $this->requireAdmin(); // Yêu cầu quyền Admin

        $event = $this->eventModel->getEventById($id);
        if (!$event) {
            $_SESSION['error'] = "Không tìm thấy sự kiện để xóa.";
            header("Location: /webdacn_quanlyclb");
            exit();
        }

        try {
            // 1. Xóa file ảnh khỏi server
            if (!empty($event['image_path'])) {
                $file_path = $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/' . $event['image_path'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }

            // 2. Xóa record khỏi DB
            $this->eventModel->deleteEvent($id);

            $_SESSION['message'] = "Đã xóa sự kiện thành công!";
            header("Location: /webdacn_quanlyclb");
            exit();
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi khi xóa sự kiện: " . $e->getMessage();
            header("Location: /webdacn_quanlyclb/event/detail/" . $id);
            exit();
        }
    }

    /**
     * === HÀM MỚI: Xử lý đăng ký tham gia sự kiện ===
     */
    public function join($event_id)
    {
        $user_id = SessionHelper::getUserId();
        $event = $this->eventModel->getEventById($event_id);
        // Yêu cầu đăng nhập và không phải Admin
        if (!SessionHelper::isLoggedIn() || SessionHelper::isAdmin()) {
            $_SESSION['error'] = "Bạn không có quyền tham gia sự kiện này.";
            header("Location: /webdacn_quanlyclb/event/detail/" . $event_id);
            exit();
        }

        // Kiểm tra sự kiện tồn tại
        if (!$event) {
            $_SESSION['error'] = "Sự kiện không tồn tại.";
            header("Location: /webdacn_quanlyclb");
            exit();
        }

        try {
            $success = $this->eventModel->joinEvent($event_id, $user_id);
            if ($success) {
                $_SESSION['message'] = "Đăng ký tham gia sự kiện thành công!";
                try {
                    // Chuẩn bị dữ liệu cho ngày quan trọng
                    $day_title = "Sự kiện: " . $event['title']; // Thêm tiền tố
                    $day_date = date('Y-m-d', strtotime($event['event_date'])); // Chỉ lấy ngày Y-m-d
                    $day_description = "Tham gia sự kiện '" . $event['title'] . "' tại " . $event['location'] . ".";

                    // Gọi hàm addDay của ImportantDayModel
                    $this->importantDayModel->addDay($user_id, $day_title, $day_date, $day_description);

                    // (Không cần thông báo thêm ở đây, thông báo join event là đủ)

                } catch (Exception $dayException) {
                    // Ghi log lỗi nếu không thêm được ngày quan trọng, nhưng không báo lỗi cho user
                    error_log("Lỗi khi tự động thêm ngày quan trọng cho sự kiện ID $event_id: " . $dayException->getMessage());
                    // Không cần set $_SESSION['error'] ở đây để tránh ghi đè thông báo thành công
                }
            } else {
                $_SESSION['error'] = "Bạn đã đăng ký tham gia sự kiện này rồi.";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Lỗi khi đăng ký: " . $e->getMessage();
        }

        header("Location: /webdacn_quanlyclb/event/detail/" . $event_id);
        exit();
    }

    /**
     * Toggle trạng thái sự kiện bằng AJAX
     */
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

        $event = $this->eventModel->getEventById($id);
        if (!$event) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy sự kiện']);
            exit();
        }

        $newStatus = $event['is_active'] ? 0 : 1;
        if ($this->eventModel->toggleActive($id, $newStatus)) {
            echo json_encode([
                'success' => true,
                'new_status' => $newStatus,
                'message' => $newStatus ? 'Đã bật sự kiện' : 'Đã tắt sự kiện'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật']);
        }
        exit();
    }
}
