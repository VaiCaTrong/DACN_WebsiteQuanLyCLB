<?php
require_once 'app/config/database.php';
require_once 'app/models/MessageModel.php';
require_once 'app/helpers/SessionHelper.php';

class ChatController {
    private $db;
    private $messageModel;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->messageModel = new MessageModel($this->db);
    }

    // API gửi tin nhắn
    public function send() {
        SessionHelper::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $receiver_id = $data['receiver_id'] ?? 0;
            $content = trim($data['content'] ?? '');

            if ($receiver_id > 0 && !empty($content)) {
                $sender_id = SessionHelper::getUserId();
                if ($this->messageModel->sendMessage($sender_id, $receiver_id, $content)) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'error' => 'Gửi thất bại']);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Dữ liệu không hợp lệ']);
            }
        }
    }

    // API lấy tin nhắn
    public function getMessages() {
        SessionHelper::requireLogin();
        $receiver_id = $_GET['receiver_id'] ?? 0;
        if ($receiver_id > 0) {
            $sender_id = SessionHelper::getUserId();
            $messages = $this->messageModel->getMessages($sender_id, $receiver_id);
            foreach ($messages as $msg) {
                if ($msg['receiver_id'] == $sender_id && !$msg['is_read']) {
                    $this->messageModel->markAsRead($msg['id'], $sender_id);
                }
            }
            echo json_encode($messages);
        } else {
            echo json_encode([]);
        }
    }

    // API kiểm tra tin nhắn mới
    public function checkNewMessages() {
        SessionHelper::requireLogin();
        $user_id = SessionHelper::getUserId();
        $unread = $this->messageModel->getUnreadMessages($user_id);
        echo json_encode(['unread_count' => count($unread)]);
    }

    // API lấy danh sách user
    public function getUsers() {
        SessionHelper::requireLogin();
        $query = "SELECT id, fullname, role FROM account WHERE id != :user_id";
        $stmt = $this->db->prepare($query);
        $user_id = SessionHelper::getUserId();
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}