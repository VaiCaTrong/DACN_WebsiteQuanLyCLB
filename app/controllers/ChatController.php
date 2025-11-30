<?php
// Bắt đầu bộ đệm đầu ra để kiểm soát lỗi
ob_start();

require_once 'app/config/database.php';
require_once 'app/models/ChatModel.php';
require_once 'app/models/FriendModel.php';
require_once 'app/helpers/SessionHelper.php';

// Kiểm tra file autoload
$autoloadPath = $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/vendor/autoload.php';
if (file_exists($autoloadPath)) {
    require_once $autoloadPath;
}

use Stichoza\GoogleTranslate\GoogleTranslate;

class ChatController
{
    private $db;
    private $chatModel;
    private $friendModel;

    public function __construct()
    {
        $this->chatModel = new ChatModel($this->db);
        $this->friendModel = new FriendModel($this->db);
    }

    public function index()
    {
        SessionHelper::requireLogin();
        $user_id = SessionHelper::getUserId();
        $friends = $this->friendModel->getFriendsList($user_id);
        $teams = $this->chatModel->getUserTeams($user_id);
        
        if (ob_get_length()) ob_end_clean();
        include_once 'app/views/chat/index.php';
    }

    /**
     * API gửi tin nhắn (Đã sửa lỗi chấm điểm bằng cURL)
     */
    public function apiSendMessage()
    {
        // Xóa bộ đệm để đảm bảo JSON sạch
        if (ob_get_length()) ob_clean();
        
        SessionHelper::requireLogin();
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Phương thức không hợp lệ.');
            }

            $sender_id = SessionHelper::getUserId();
            $receiver_id = $_POST['receiver_id'] ?? null;
            $team_id = $_POST['team_id'] ?? null;
            $message_text = $_POST['message'] ?? '';
            $reply_to_id = $_POST['reply_to_message_id'] ?? null;

            $has_text = !empty(trim($message_text));
            $has_image = isset($_FILES['chat_image']) && $_FILES['chat_image']['error'] == UPLOAD_ERR_OK;

            if (!$has_text && !$has_image) {
                throw new Exception('Nội dung tin nhắn không được rỗng.');
            }

            $toxicityScore = null;
            $censoredText = $message_text;
            $censoredEnglish = null;
            $image_path = null;

            // --- 1. XỬ LÝ VĂN BẢN ---
            if ($has_text) {
                // Dịch sang tiếng Anh (để chấm điểm cho chuẩn)
                $translatedText = $this->translateToEnglish($message_text);
                
                // Nếu dịch lỗi, dùng tạm text gốc
                $textToAnalyze = $translatedText ? $translatedText : $message_text;

                // Chấm điểm độc hại (Dùng cURL thay vì Guzzle)
                $toxicityScore = $this->analyzeMessageToxicity($textToAnalyze);

                // Che từ nhạy cảm (***)
                $censoredText = $this->censorText($message_text);
                
                // Lưu bản dịch tiếng Anh (đã che) - Tùy chọn
                $censoredEnglish = $this->translateToEnglish($censoredText);
            }

            // --- 2. XỬ LÝ ẢNH ---
            if ($has_image) {
                $image_path = $this->handleImageUpload($_FILES['chat_image']);
            }

            // --- 3. CHUẨN BỊ DỮ LIỆU GỬI ---
            $message_type = $has_image ? 'image' : 'text';
            $final_content = $has_image ? $image_path : $censoredText;
            // Nếu là ảnh thì không có tiếng Anh, nếu là text thì lấy bản đã dịch
            $final_content_english = $has_image ? null : $censoredEnglish;

            // --- 4. LƯU VÀO DATABASE ---
            $result = false;
            if ($team_id) {
                if ($this->chatModel->isUserInTeam($sender_id, $team_id) || SessionHelper::isAdmin()) {
                    $result = $this->chatModel->sendGroupMessage(
                        $sender_id, $team_id, $final_content, $message_type, 
                        $reply_to_id, $toxicityScore, $final_content_english
                    );
                } else {
                    throw new Exception('Bạn không có quyền gửi tin nhắn vào nhóm này!');
                }
            } elseif ($receiver_id) {
                $result = $this->chatModel->sendPrivateMessage(
                    $sender_id, $receiver_id, $final_content, $message_type, 
                    $reply_to_id, $toxicityScore, $final_content_english
                );
            } else {
                throw new Exception('Người nhận không hợp lệ!');
            }

            if (!$result) {
                throw new Exception('Lỗi cơ sở dữ liệu khi lưu tin nhắn.');
            }

            echo json_encode(['success' => true, 'message' => 'Tin nhắn đã được gửi!', 'score' => $toxicityScore]);

        } catch (Exception $e) {
            error_log("API Send Message Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * Hàm chấm điểm độc hại sử dụng cURL (Giống hệt file test_api.php)
     * Đã loại bỏ Guzzle để tránh lỗi SSL trên localhost
     */
    private function analyzeMessageToxicity($text)
    {
        $configFile = APP_ROOT . '/app/config/config.php';
        $apiKey = '';
        if (file_exists($configFile)) {
            $config = require $configFile;
            $apiKey = $config['api']['perspective_key'] ?? '';
        }

        if (empty($apiKey) || str_contains($apiKey, '')) {
            error_log("Perspective API Key missing or invalid.");
            return 0;
        }

        $url = 'https://commentanalyzer.googleapis.com/v1alpha1/comments:analyze?key=' . $apiKey;

        // Cấu trúc dữ liệu JSON gửi lên Google
        $data = [
            'comment' => ['text' => $text],
            'languages' => ['en'], // Luôn để EN vì ta đã dịch rồi
            'requestedAttributes' => ['TOXICITY' => new stdClass()]
        ];

        // --- BẮT ĐẦU CURL (Code từ file test của bạn) ---
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // QUAN TRỌNG: Tắt kiểm tra SSL để chạy được trên Localhost/Laragon
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // --- XỬ LÝ KẾT QUẢ ---
        
        if ($curlError) {
            error_log("Perspective cURL Error: " . $curlError);
            return 0;
        }

        if ($httpCode == 200) {
            $result = json_decode($response, true);
            if (isset($result['attributeScores']['TOXICITY']['summaryScore']['value'])) {
                $score = $result['attributeScores']['TOXICITY']['summaryScore']['value'];
                // Ghi log để bạn kiểm tra xem điểm số là bao nhiêu
                error_log("Perspective Score for '{$text}': " . $score);
                return $score;
            }
        } else {
            error_log("Perspective API Failed. Code: $httpCode. Response: " . $response);
        }

        return 0; // Mặc định trả về 0 nếu lỗi
    }

    /**
     * Hàm che từ nhạy cảm
     */
    private function censorText($text)
    {
        $badWords = [
            'đm', 'dm', 'đkm', 'vcl', 'vl', 'đéo', 'deo', 'chó', 'ngu', 'óc chó', 
            'cút', 'giết', 'chết', 'đĩ', 'lồn', 'buồi', 'cặc', 'dái', 'phò',
            'thằng điên', 'con điên', 'mất dạy', 'khốn nạn', 'mẹ', 'má',
            'fuck', 'shit', 'bitch', 'bastard', 'dick', 'pussy', 'asshole', 
            'cunt', 'whore', 'slut', 'nigger', 'faggot', 'kill', 'die', 'đụ'
        ];

        foreach ($badWords as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/iu';
            $text = preg_replace($pattern, '***', $text);
        }
        return $text;
    }

    /**
     * Dịch sang tiếng Anh
     */
    private function translateToEnglish($text)
    {
        if (!class_exists('Stichoza\GoogleTranslate\GoogleTranslate')) {
            return $text;
        }

        try {
            $tr = new GoogleTranslate('en'); 
            $tr->setSource('vi');
            return $tr->translate($text);
        } catch (Exception $e) {
            error_log("Translation Error: " . $e->getMessage());
            return $text;
        }
    }

    // --- CÁC API KHÁC (Đã dọn dẹp buffer) ---

    public function apiGetMessages()
    {
        if (ob_get_length()) ob_clean();
        SessionHelper::requireLogin();
        header('Content-Type: application/json');
        
        $user_id = SessionHelper::getUserId();
        $friend_id = $_GET['friend_id'] ?? null;
        $team_id = $_GET['team_id'] ?? null;
        $messages = [];
        
        if ($team_id && ($this->chatModel->isUserInTeam($user_id, $team_id) || SessionHelper::isAdmin())) {
            $messages = $this->chatModel->getGroupMessages($team_id);
        } elseif ($friend_id) {
            $messages = $this->chatModel->getPrivateMessages($user_id, $friend_id);
        }
        
        echo json_encode([
            'success' => true,
            'messages' => $messages,
            'isAdmin' => SessionHelper::isAdmin()
        ]);
        exit;
    }

    public function apiDeleteMessage()
    {
        if (ob_get_length()) ob_clean();
        SessionHelper::requireLogin();
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents('php://input'), true);
        $messageId = $data['messageId'] ?? null;
        $messageType = $data['messageType'] ?? null;
        $userId = SessionHelper::getUserId();
        
        if (!$messageId || !$messageType) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
            exit;
        }
        
        $success = $this->chatModel->deleteMessage($messageId, $userId, $messageType);
        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Đã xóa tin nhắn.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền xóa tin nhắn này.']);
        }
        exit;
    }

    public function apiClearChatHistory()
    {
        if (ob_get_length()) ob_clean();
        SessionHelper::requireLogin();
        header('Content-Type: application/json');

        $friend_id = $_POST['friend_id'] ?? null;
        $team_id = $_POST['team_id'] ?? null;
        $current_user_id = SessionHelper::getUserId();

        if (!$friend_id && !$team_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin.']);
            exit;
        }

        $success = false;
        if ($friend_id) {
            $success = $this->chatModel->clearPrivateChatHistory($current_user_id, $friend_id);
        } elseif ($team_id) {
            $is_admin = SessionHelper::isAdmin();
            $is_leader = $this->chatModel->isUserTeamLeader($current_user_id, $team_id);

            if ($is_admin || $is_leader) {
                $success = $this->chatModel->clearGroupChatHistory($team_id);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không có quyền.']);
                exit;
            }
        }

        if ($success) {
            echo json_encode(['success' => true, 'message' => 'Đã xóa lịch sử.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi xóa lịch sử.']);
        }
        exit;
    }

    public function apiUnlockGroupChat()
    {
        if (ob_get_length()) ob_clean();
        SessionHelper::requireLogin();
        header('Content-Type: application/json');
        
        if (!SessionHelper::isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Không có quyền.']);
            exit;
        }

        $team_id = $_POST['team_id'] ?? null;
        if ($this->chatModel->unlockGroupChat($team_id)) {
            echo json_encode(['success' => true, 'message' => 'Mở khóa thành công.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi mở khóa.']);
        }
        exit;
    }

    private function handleImageUpload($file)
    {
        $uploadDir = 'public/uploads/chat_images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) throw new Exception('File không phải ảnh.');
        
        $fileName = uniqid() . '-' . basename($file['name']);
        $targetFile = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $targetFile;
        } else {
            throw new Exception('Lỗi upload ảnh.');
        }
    }
}
?>