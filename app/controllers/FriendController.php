<?php
require_once 'app/config/database.php';
require_once 'app/models/FriendModel.php';
require_once 'app/models/AccountModel.php';
require_once 'app/helpers/SessionHelper.php';
require_once 'app/models/MessageModel.php'; // Thêm dòng này

class FriendController
{
    private $friendModel;
    private $accountModel;
    private $db;
    private $messageModel;

    public function __construct()
    {
        $this->friendModel = new FriendModel($this->db);
        $this->accountModel = new AccountModel($this->db);
        $this->messageModel = new MessageModel($this->db); // Thêm dòng này
    }

    // Hiển thị trang tìm bạn bè với danh sách tất cả user
    public function searchFriends()
    {
        SessionHelper::requireLogin();
        $query = $_GET['q'] ?? '';
        $user_id = SessionHelper::getUserId();
        $results = [];
        if (!empty($query)) {
            $results = $this->friendModel->searchUsers($query, $user_id);
        } else {
            $results = $this->friendModel->getAllUsers($user_id);
        }
        include_once 'app/views/friend/search_friends.php';
    }

    // API tìm kiếm user cho autocomplete
    public function searchUsers()
    {
        SessionHelper::requireLogin();
        header('Content-Type: application/json');
        $query = $_GET['q'] ?? '';
        $user_id = SessionHelper::getUserId();
        $results = $this->friendModel->searchUsers($query, $user_id);
        echo json_encode(array_slice($results, 0, 5)); // Giới hạn 5 kết quả
    }

    // Gửi yêu cầu kết bạn
    public function sendFriendRequest()
    {
        SessionHelper::requireLogin();
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $receiver_id = $data['receiver_id'] ?? 0;
            $sender_id = SessionHelper::getUserId();

            if ($receiver_id > 0 && $sender_id != $receiver_id) {
                $friend_id = $this->friendModel->sendFriendRequest($sender_id, $receiver_id);
                if ($friend_id) {
                    // Tạo notification cho người nhận
                    $sender = $this->accountModel->getAccountById($sender_id);
                    $title = 'Yêu cầu kết bạn';
                    $message = "{$sender['fullname']} đã gửi yêu cầu kết bạn.";
                    $link = "/webdacn_quanlyclb/account/notifications";
                    $this->createNotification($receiver_id, $title, $message, $link, $friend_id);
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Yêu cầu đã tồn tại hoặc lỗi']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            }
        }
    }

    // Chấp nhận yêu cầu kết bạn
    public function acceptFriend()
    {
        SessionHelper::requireLogin();
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $friend_id = $data['friend_id'] ?? 0;
            $notification_id = $data['notification_id'] ?? 0;
            $user_id = SessionHelper::getUserId();

            if ($friend_id > 0 && $this->friendModel->acceptFriendRequest($friend_id, $user_id)) {
                // Xóa thông báo
                if ($notification_id > 0) {
                    $this->deleteNotification($notification_id, $user_id);
                }
                // Gửi thông báo cho người gửi yêu cầu
                $friend_data = $this->getFriendData($friend_id);
                $sender_id = ($friend_data['user_id1'] == $user_id) ? $friend_data['user_id2'] : $friend_data['user_id1'];
                $receiver = $this->accountModel->getAccountById($user_id);
                $title = 'Kết bạn thành công';
                $message = "{$receiver['fullname']} đã chấp nhận yêu cầu kết bạn của bạn.";
                $link = "/webdacn_quanlyclb/friend/searchFriends";
                $this->createNotification($sender_id, $title, $message, $link, null);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi chấp nhận yêu cầu']);
            }
        }
    }

    // Từ chối yêu cầu kết bạn
    public function rejectFriend()
    {
        SessionHelper::requireLogin();
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $friend_id = $data['friend_id'] ?? 0;
            $notification_id = $data['notification_id'] ?? 0;
            $user_id = SessionHelper::getUserId();

            if ($friend_id > 0 && $this->friendModel->rejectFriendRequest($friend_id, $user_id)) {
                // Xóa thông báo
                if ($notification_id > 0) {
                    $this->deleteNotification($notification_id, $user_id);
                }
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi từ chối yêu cầu']);
            }
        }
    }

    // Hiển thị danh sách bạn bè của người dùng
    public function friendsList()
    {
        SessionHelper::requireLogin();
        $user_id = SessionHelper::getUserId();
        $friends = $this->friendModel->getFriendsList($user_id);
        include_once 'app/views/friend/friends_list.php';
    }
    // Lấy danh sách bạn bè
    public function getFriends()
    {
        SessionHelper::requireLogin();
        header('Content-Type: application/json');
        $user_id = SessionHelper::getUserId();
        $friends = $this->friendModel->getFriends($user_id);
        echo json_encode($friends);
    }

    // Tạo thông báo
    private function createNotification($user_id, $title, $message, $link = null, $friend_id = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, title, message, link, friend_id, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$user_id, $title, $message, $link, $friend_id]);
    }

    // Xóa thông báo
    private function deleteNotification($notification_id, $user_id)
    {
        $stmt = $this->db->prepare("
            DELETE FROM notifications WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$notification_id, $user_id]);
    }

    // Lấy dữ liệu friend relationship
    private function getFriendData($friend_id)
    {
        $stmt = $this->db->prepare("
            SELECT user_id1, user_id2 FROM friends WHERE id = ?
        ");
        $stmt->execute([$friend_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm vào FriendController.php
    public function sendMessage($receiver_id = null)
    {
        SessionHelper::requireLogin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Nhận receiver_id từ parameter hoặc POST data
            $receiver_id = $receiver_id ?: ($_POST['receiver_id'] ?? 0);
            $message = $_POST['message'] ?? '';
            $sender_id = SessionHelper::getUserId();

            if ($receiver_id > 0 && !empty(trim($message)) && $sender_id != $receiver_id) {
                $success = $this->messageModel->sendMessage($sender_id, $receiver_id, trim($message));

                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Tin nhắn đã gửi!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Lỗi khi gửi tin nhắn!']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ!']);
            }
        }
        exit;
    }

    public function getMessages($friend_id = null)
    {
        SessionHelper::requireLogin();
        header('Content-Type: application/json');

        // Nhận friend_id từ parameter hoặc GET data
        $friend_id = $friend_id ?: ($_GET['friend_id'] ?? 0);
        $user_id = SessionHelper::getUserId();

        if ($friend_id > 0) {
            $messages = $this->messageModel->getMessages($user_id, $friend_id);

            echo json_encode([
                'success' => true,
                'messages' => $messages
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Friend ID không hợp lệ!']);
        }
        exit;
    }
    // THÊM VÀO FriendController.php
    public function apiSendMessage()
    {
        SessionHelper::requireLogin();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $receiver_id = $_POST['receiver_id'] ?? 0;
            $message = $_POST['message'] ?? '';
            $sender_id = SessionHelper::getUserId();

            if ($receiver_id > 0 && !empty(trim($message)) && $sender_id != $receiver_id) {
                $success = $this->messageModel->sendMessage($sender_id, $receiver_id, trim($message));

                if ($success) {
                    echo json_encode(['success' => true, 'message' => 'Tin nhắn đã gửi!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Lỗi khi gửi tin nhắn!']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ!']);
            }
        }
        exit;
    }

    public function apiGetMessages()
    {
        SessionHelper::requireLogin();
        header('Content-Type: application/json');

        $friend_id = $_GET['friend_id'] ?? 0;
        $user_id = SessionHelper::getUserId();

        if ($friend_id > 0) {
            $messages = $this->messageModel->getMessages($user_id, $friend_id);

            echo json_encode([
                'success' => true,
                'messages' => $messages
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Friend ID không hợp lệ!']);
        }
        exit;
    }
    
}
?>