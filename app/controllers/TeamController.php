<?php
require_once 'app/config/database.php';
require_once 'app/models/TeamModel.php';
require_once 'app/helpers/SessionHelper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/controllers/BaseController.php';

class TeamController extends BaseController
{
    private $teamModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->teamModel = new TeamModel();
    }

    private function isAdmin()
    {
        return SessionHelper::isAdmin();
    }

    private function getUserId()
    {
        return SessionHelper::getUserId();
    }

    private function createNotification($user_id, $title, $message, $link = null)
    {
        $stmt = $this->db->prepare("INSERT INTO notifications (user_id, title, message, link, created_at) VALUES (:user_id, :title, :message, :link, NOW())");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':link', $link);
        return $stmt->execute();
    }

    public function index()
    {
        $teams = $this->teamModel->getAllTeams();
        $user_id = SessionHelper::getUserId();
        $current_team_id = $this->teamModel->getCurrentTeamId($user_id);
        require_once 'app/views/team/list.php';
    }

    public function add()
    {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập chức năng này!";
            exit;
        }
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $quantity_user = $_POST['quantity_user'] ?? 1;
            $talent = $_POST['talent'] ?? '';
            $note = $_POST['note'] ?? '';
            $user_id = $this->getUserId();
            $avatar_team = $_POST['avatar_team'] ?? '';

            if (isset($_FILES['avatar_team']) && $_FILES['avatar_team']['error'] == 0) {
                try {
                    $avatar_team = $this->uploadImage($_FILES['avatar_team']);
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }

            if (!$error && $this->teamModel->addTeam($name, $description, $quantity_user, $talent, $note, $user_id, $avatar_team)) {
                header('Location: /webdacn_quanlyclb/Team');
                exit;
            } else {
                $error = $error ?: "Đã xảy ra lỗi khi thêm team.";
            }
        }
        include 'app/views/team/add.php';
    }

    public function edit($id)
    {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập chức năng này!";
            exit;
        }
        $team = $this->teamModel->getTeamById($id);
        if (!$team) {
            echo "Không tìm thấy team.";
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? $team['name'];
            $description = $_POST['description'] ?? $team['description'];
            $quantity_user = $_POST['quantity_user'] ?? $team['quantity_user'];
            $talent = $_POST['talent'] ?? $team['talent'];
            $note = $_POST['note'] ?? $team['note'];
            $user_id = $this->getUserId();
            $avatar_team = $_POST['avatar_team'] ?? $team['avatar_team'];

            if (isset($_FILES['avatar_team']) && $_FILES['avatar_team']['error'] == 0) {
                $avatar_team = $this->uploadImage($_FILES['avatar_team']);
            }

            if ($this->teamModel->updateTeam($id, $name, $description, $quantity_user, $talent, $note, $user_id, $avatar_team)) {
                header('Location: /webdacn_quanlyclb/Team');
            } else {
                echo "Đã xảy ra lỗi khi cập nhật team.";
            }
        }
        include 'app/views/team/edit.php';
    }

    public function delete($id)
    {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập chức năng này!";
            exit;
        }
        if ($this->teamModel->deleteTeam($id)) {
            header('Location: /webdacn_quanlyclb/Team');
        } else {
            echo "Đã xảy ra lỗi khi xóa team.";
        }
    }

    public function myTeam()
    {
        $user_id = SessionHelper::getUserId();
        $current_team_id = $this->teamModel->getCurrentTeamId($user_id);

        if (!$current_team_id) {
            $_SESSION['error'] = 'Bạn chưa tham gia đội nào.';
            header('Location: /webdacn_quanlyclb/Team');
            exit;
        }

        $team = $this->teamModel->getTeamById($current_team_id);
        $members = $this->teamModel->getTeamMembers($current_team_id);
        $messages = $this->teamModel->getTeamMessages($current_team_id);
        $member_count = $this->teamModel->countTeamMembers($current_team_id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
            $message = $_POST['message'];
            $this->teamModel->sendTeamMessage($user_id, $current_team_id, $message);
            header('Location: /webdacn_quanlyclb/Team/myTeam');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'leave') {
            if ($this->teamModel->leaveTeam($user_id)) {
                $_SESSION['message'] = 'Bạn đã rời khỏi CLB thành công. Cảm ơn bạn đã tham gia!';
                header('Location: /webdacn_quanlyclb/Team');
            } else {
                $_SESSION['error'] = 'Có lỗi khi rời đội.';
                header('Location: /webdacn_quanlyclb/Team/myTeam');
            }
            exit;
        }

        require_once 'app/views/team/myTeam.php';
    }

    private function uploadImage($file)
    {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if ($file["size"] > 10 * 1024 * 1024) {
            throw new Exception("Hình ảnh có kích thước quá lớn.");
        }
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Có lỗi xảy ra khi tải lên hình ảnh.");
        }
        return $target_file;
    }

    public function save()
    {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập chức năng này!";
            exit;
        }
    }

    public function update($id)
    {
        if (!$this->isAdmin()) {
            echo "Bạn không có quyền truy cập chức năng này!";
            exit;
        }
    }

    public function view($id)
    {
        $team = $this->teamModel->getTeamById($id);

        if ($team) {
            include 'app/views/team/view.php';
        } else {
            echo "Không tìm thấy đội nhóm.";
        }
    }

    public function request()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['name'], $_POST['reason'], $_POST['talent'])) {
            $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
            $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $khoa = isset($_POST['khoa']) ? filter_var($_POST['khoa'], FILTER_SANITIZE_STRING) : null;
            $reason = filter_var($_POST['reason'], FILTER_SANITIZE_STRING);
            $talent = filter_var($_POST['talent'], FILTER_SANITIZE_STRING);
            $avatar_team = null;

            if (empty($reason) || empty($talent)) {
                SessionHelper::set('error', "Lý do và tài năng là bắt buộc!");
                header('Location: /webdacn_quanlyclb/Team');
                exit;
            }

            if (isset($_FILES['avatar_team']) && $_FILES['avatar_team']['error'] == 0) {
                try {
                    $avatar_team = $this->uploadImage($_FILES['avatar_team']);
                } catch (Exception $e) {
                    SessionHelper::set('error', $e->getMessage());
                    header('Location: /webdacn_quanlyclb/Team');
                    exit;
                }
            }

            $teams = $this->teamModel->getAllTeamRequests();
            do {
                $team_id = 'REQ_' . strtoupper(substr(md5(uniqid()), 0, 8));
                $existing_team_id = $this->teamModel->getTeamById($team_id);
            } while ($existing_team_id || in_array($team_id, array_column($teams, 'id')));

            $data = [
                'team_id' => $team_id,
                'user_id' => $user_id,
                'name' => $name,
                'khoa' => $khoa,
                'reason' => $reason,
                'talent' => $talent,
                'created_at' => date('Y-m-d H:i:s'),
                'avatar_team' => $avatar_team
            ];

            if ($this->teamModel->createTeamRequest($data)) {
                SessionHelper::set('message', "Yêu cầu tạo câu lạc bộ đã được gửi thành công!");
            } else {
                SessionHelper::set('error', "Có lỗi xảy ra khi gửi yêu cầu!");
            }
            header('Location: /webdacn_quanlyclb/Team');
            exit;
        }
        header('Location: /webdacn_quanlyclb/Team');
        exit;
    }

    public function requests()
    {
        if (!SessionHelper::isAdmin()) {
            SessionHelper::set('error', "Bạn không có quyền truy cập trang này!");
            header('Location: /webdacn_quanlyclb');
            exit;
        }

        $requests = $this->teamModel->getAllTeamRequests();
        require_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/team/requests.php';
    }

    public function approveRequest()
    {
        if (!SessionHelper::isAdmin()) {
            SessionHelper::set('error', "Bạn không có quyền thực hiện hành động này!");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Không có quyền!']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $team_id = $data['team_id'] ?? null;

        if (!$team_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Thiếu mã đội!']);
            exit;
        }

        $request = $this->teamModel->getTeamRequestById($team_id);
        if (!$request) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không tồn tại!']);
            exit;
        }

        $success = $this->teamModel->addTeam(
            $request['name'],
            $request['reason'],
            1,
            $request['talent'],
            $request['khoa'] ?? '',
            $request['user_id'],
            $request['avatar_team']
        );

        if ($success) {
            $stmt = $this->db->prepare("UPDATE account SET role = 'staff' WHERE id = :user_id");
            $stmt->bindParam(':user_id', $request['user_id'], PDO::PARAM_INT);
            $stmt->execute();

            $currentTime = date('Y-m-d H:i:s');
            $this->createNotification(
                $request['user_id'],
                'Yêu cầu tạo CLB đã được duyệt',
                'Yêu cầu tạo câu lạc bộ "' . htmlspecialchars($request['name']) . '" của bạn đã được duyệt vào. Bạn đã được thăng cấp thành staff!',
                '/webdacn_quanlyclb/account/profile/edit'
            );

            $this->teamModel->deleteTeamRequest($team_id);

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Duyệt yêu cầu thành công!']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Lỗi khi tạo đội!']);
        }
        exit;
    }

    public function rejectRequest()
    {
        if (!SessionHelper::isAdmin()) {
            SessionHelper::set('error', "Bạn không có quyền thực hiện hành động này!");
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Không có quyền!']);
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $team_id = $data['team_id'] ?? null;

        if (!$team_id) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Thiếu mã đội!']);
            exit;
        }

        $request = $this->teamModel->getTeamRequestById($team_id);
        if (!$request) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không tồn tại!']);
            exit;
        }

        $success = $this->teamModel->deleteTeamRequest($team_id);
        if ($success) {
            $currentTime = date('Y-m-d H:i:s');
            $this->createNotification(
                $request['user_id'],
                'Yêu cầu của bạn bị từ chối',
                'Yêu cầu tạo câu lạc bộ "' . htmlspecialchars($request['name']) . '" của bạn đã bị từ chối vào.',
                null
            );

            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Đã từ chối yêu cầu thành công!']);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa yêu cầu!']);
        }
        exit;
    }

    // Trong TeamController.php, phương thức join
    public function join()
    {
        $user_id = SessionHelper::getUserId();
        $current_team_id = $this->teamModel->getCurrentTeamId($user_id);
        $team_id = $this->teamModel->getTeamIdFromAccount($user_id);
        if ($team_id) {
            $_SESSION['error'] = "Bạn đã tham gia một đội. Vui lòng rời đội hiện tại trước khi tham gia đội mới!";
            header("Location: /webdacn_quanlyclb/Team/list");
            exit();
        }
        $team_id = isset($_GET['team_id']) ? (int)$_GET['team_id'] : null;

        if (!$user_id || !$team_id) {
            header('Location: /webdacn_quanlyclb/Team');
            exit;
        }

        $message = '';
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $date_of_birth = $_POST['date_of_birth'] ?? '';
            $khoa = $_POST['khoa'] ?? '';
            $reason = $_POST['reason'] ?? '';
            $talent = $_POST['talent'] ?? null;

            if (empty($name) || empty($reason)) {
                $error = 'Tên và lý do là bắt buộc.';
            } elseif ($current_team_id === null) {
                // Gọi phương thức đã sửa để tự động lấy leader
                if ($this->teamModel->saveJoinRequest($user_id, $team_id, $name, $date_of_birth, $khoa, $reason, $talent)) {
                    $message = 'Bạn đã gửi đơn xin tham gia thành công!';
                } else {
                    $error = 'Có lỗi xảy ra khi gửi yêu cầu.';
                }
            } else {
                $error = 'Bạn đã tham gia một đội, không thể gửi thêm yêu cầu.';
            }

            $_SESSION['message'] = $message;
            $_SESSION['error'] = $error;
            header('Location: /webdacn_quanlyclb/Team/join?team_id=' . $team_id);
            exit;
        }

        require_once 'app/views/team/jointeam.php';
    }

    // Trong TeamController.php, sửa phương thức userJoin
    public function userJoin()
    {
        $user_id = SessionHelper::getUserId();
        $message = '';
        $error = '';
        $approver_user_id = $user_id;
        $currentTime = date('Y-m-d H:i:s');

        // Chỉ lấy các join_requests có leader trùng với user_id hiện tại
        $join_requests = $this->teamModel->getJoinRequestsByLeader($user_id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $join_form_id = $_POST['join_form_id'] ?? 0;

            // Kiểm tra xem join_form có thuộc về leader này không
            $stmt = $this->db->prepare("SELECT user_id, team_id, name, leader FROM join_form WHERE id = :join_form_id AND leader = :leader_id");
            $stmt->bindParam(':join_form_id', $join_form_id, PDO::PARAM_INT);
            $stmt->bindParam(':leader_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $request = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$request) {
                $error = 'Yêu cầu không tồn tại hoặc bạn không có quyền xử lý!';
                $_SESSION['error'] = $error;
                header('Location: /webdacn_quanlyclb/Team/userjoin');
                exit;
            }

            $user_id_request = $request['user_id'];
            $team_id = $request['team_id'];
            $requester_name = $request['name'] ?? 'Người dùng';

            $team = $this->teamModel->getTeamById($team_id);
            $team_name = $team['name'] ?? 'Câu lạc bộ';

            if ($action === 'approve') {
                if ($this->teamModel->approveJoinRequest($join_form_id, $approver_user_id)) {
                    $message = 'Đã duyệt yêu cầu thành công!';
                    $this->createNotification(
                        $user_id_request,
                        'Yêu cầu tham gia được duyệt',
                        "Xin chúc mừng, yêu cầu tham gia câu lạc bộ '$team_name' của bạn đã được duyệt.",
                        '/webdacn_quanlyclb/account/profile/edit'
                    );
                    $this->createNotification(
                        $approver_user_id,
                        'Đã duyệt yêu cầu tham gia',
                        "Bạn đã duyệt yêu cầu tham gia câu lạc bộ '$team_name' của $requester_name.",
                        '/webdacn_quanlyclb/Team/userjoin'
                    );
                } else {
                    $error = 'Có lỗi khi duyệt yêu cầu!';
                }
            } elseif ($action === 'schedule') {
                if ($this->teamModel->scheduleInterview($join_form_id)) {
                    $message = 'Đã hẹn phỏng vấn thành công!';
                    $this->createNotification(
                        $user_id_request,
                        'Hẹn phỏng vấn',
                        "Xin chúc mừng, bạn được hẹn phỏng vấn cho câu lạc bộ '$team_name'. Hãy đợi thông tin ngày phỏng vấn nhé!!!",
                        '/webdacn_quanlyclb/account/profile/edit'
                    );
                    $this->createNotification(
                        $approver_user_id,
                        'Đã hẹn phỏng vấn',
                        "Bạn đã hẹn phỏng vấn cho $requester_name tham gia câu lạc bộ '$team_name' .",
                        '/webdacn_quanlyclb/Team/userjoin'
                    );
                } else {
                    $error = 'Có lỗi khi hẹn phỏng vấn!';
                }
            } elseif ($action === 'reject') {
                if ($this->teamModel->rejectJoinRequest($join_form_id)) {
                    $message = 'Đã từ chối yêu cầu thành công!';
                    $this->createNotification(
                        $user_id_request,
                        'Yêu cầu tham gia bị từ chối',
                        "Thật tiếc, yêu cầu tham gia câu lạc bộ '$team_name' của bạn không được duyệt. Hẹn bạn lần sau!",
                        '/webdacn_quanlyclb/account/profile/edit'
                    );
                    $this->createNotification(
                        $approver_user_id,
                        'Đã từ chối yêu cầu tham gia',
                        "Bạn đã từ chối yêu cầu tham gia câu lạc bộ '$team_name' của $requester_name .",
                        '/webdacn_quanlyclb/Team/userjoin'
                    );
                } else {
                    $error = 'Có lỗi khi từ chối yêu cầu!';
                }
            }

            // Lấy lại danh sách sau khi xử lý
            $join_requests = $this->teamModel->getJoinRequestsByLeader($user_id);

            $_SESSION['message'] = $message;
            $_SESSION['error'] = $error;
            header('Location: /webdacn_quanlyclb/Team/userjoin');
            exit;
        }

        require_once 'app/views/team/userjoin.php';
    }

    // Add this method to TeamController.php
    public function manageTeam()
    {
        if (!SessionHelper::isStaff()) {
            SessionHelper::set('error', "Bạn không có quyền truy cập chức năng này!");
            header('Location: /webdacn_quanlyclb');
            exit;
        }

        $user_id = $this->getUserId();
        $team = $this->teamModel->getTeamByUserId($user_id);

        if (!$team) {
            SessionHelper::set('error', 'Bạn không phải là quản lý của bất kỳ câu lạc bộ nào!');
            header('Location: /webdacn_quanlyclb');
            exit;
        }

        $members = $this->teamModel->getTeamMembers($team['id']);
        $join_requests = $this->teamModel->getJoinRequestsForTeam($team['id']);

        require_once 'app/views/team/manageTeam.php';
    }

    public function punish()
    {
        if (!SessionHelper::isStaff()) {
            SessionHelper::set('error', "Bạn không có quyền thực hiện hành động này!");
            header('Location: /webdacn_quanlyclb/Team/manageTeam');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
            $team_id = filter_var($_POST['team_id'], FILTER_VALIDATE_INT);
            $reason = filter_var($_POST['reason'], FILTER_SANITIZE_STRING);
            $severity = $_POST['severity'] ?? '';
            $created_by = $this->getUserId();
            $currentTime = date('Y-m-d H:i:s');

            if (!$user_id || !$team_id || !$reason || !in_array($severity, ['light', 'medium', 'heavy'])) {
                SessionHelper::set('error', "Dữ liệu không hợp lệ!");
                header('Location: /webdacn_quanlyclb/Team/manageTeam');
                exit;
            }

            if ($this->teamModel->punishMember($user_id, $team_id, $reason, $severity, $created_by)) {
                $team = $this->teamModel->getTeamById($team_id);
                $team_name = $team['name'] ?? 'Câu lạc bộ';
                $points_deducted = $severity === 'light' ? 5 : ($severity === 'medium' ? 10 : 15);

                // Notify the punished member
                $this->createNotification(
                    $user_id,
                    'Thông báo phạt',
                    "Bạn đã bị phạt $points_deducted điểm trong câu lạc bộ '$team_name' vì: $reason.",
                    '/webdacn_quanlyclb/Team/myTeam'
                );

                // Notify the staff who issued the punishment
                $stmt = $this->db->prepare("SELECT fullname FROM account WHERE id = :user_id");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $punished_user = $stmt->fetch(PDO::FETCH_ASSOC);
                $punished_name = $punished_user['fullname'] ?? 'Người dùng';

                $this->createNotification(
                    $created_by,
                    'Đã thực hiện phạt',
                    "Bạn đã phạt $punished_name $points_deducted điểm trong câu lạc bộ '$team_name' vì: $reason.",
                    '/webdacn_quanlyclb/Team/manageTeam'
                );

                SessionHelper::set('message', "Đã xử lý phạt thành công!");
            } else {
                SessionHelper::set('error', "Có lỗi xảy ra khi xử lý phạt!");
            }
            header('Location: /webdacn_quanlyclb/Team/manageTeam');
            exit;
        }
        header('Location: /webdacn_quanlyclb/Team/manageTeam');
        exit;
    }
    public function reward()
    {
        if (!SessionHelper::isStaff()) {
            SessionHelper::set('error', "Bạn không có quyền thực hiện hành động này!");
            header('Location: /webdacn_quanlyclb/Team/manageTeam');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);
            $team_id = filter_var($_POST['team_id'], FILTER_VALIDATE_INT);
            $reason = filter_var($_POST['reason'], FILTER_SANITIZE_STRING);
            $severity = $_POST['severity'] ?? '';
            $created_by = $this->getUserId();
            $currentTime = date('Y-m-d H:i:s');

            if (!$user_id || !$team_id || !$reason || !in_array($severity, ['temporary', 'good', 'excellent'])) {
                SessionHelper::set('error', "Dữ liệu không hợp lệ!");
                header('Location: /webdacn_quanlyclb/Team/manageTeam');
                exit;
            }

            if ($this->teamModel->rewardMember($user_id, $team_id, $reason, $severity, $created_by)) {
                $team = $this->teamModel->getTeamById($team_id);
                $team_name = $team->name ?? "";
                $points_added = $severity === 'temporary' ? 5 : ($severity === 'good' ? 10 : 15);

                // Notify the rewarded member
                $this->createNotification(
                    $user_id,
                    'Thông báo thưởng',
                    "Xin chúc mừng, bạn đã được thưởng $points_added điểm trong câu lạc bộ '$team_name' vì: $reason.",
                    '/webdacn_quanlyclb/Team/myTeam'
                );

                // Notify the staff who issued the reward
                $stmt = $this->db->prepare("SELECT fullname FROM account WHERE id = :user_id");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $rewarded_user = $stmt->fetch(PDO::FETCH_ASSOC);
                $rewarded_name = $rewarded_user['fullname'] ?? 'Người dùng';

                $this->createNotification(
                    $created_by,
                    'Đã thực hiện thưởng',
                    "Bạn đã thưởng $points_added điểm cho $rewarded_name trong câu lạc bộ '$team_name' vì: $reason.",
                    '/webdacn_quanlyclb/Team/manageTeam'
                );

                SessionHelper::set('message', "Đã xử lý thưởng thành công!");
            } else {
                SessionHelper::set('error', "Có lỗi xảy ra khi xử lý thưởng!");
            }
            header('Location: /webdacn_quanlyclb/Team/manageTeam');
            exit;
        }
        header('Location: /webdacn_quanlyclb/Team/manageTeam');
        exit;
    }
}
