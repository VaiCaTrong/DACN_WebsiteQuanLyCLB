<?php
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../models/EventModel.php';
require_once __DIR__ . '/../models/AdvertisingBannerModel.php';
require_once 'app/helpers/SessionHelper.php';

class DefaultController
{
    private $postModel;
    private $userModel;
    private $eventModel;
    private $bannerModel;
    private $db;

    public function __construct()
    {
        $this->postModel = new PostModel();
        $this->userModel = new UserModel();
        $this->eventModel = new EventModel();
        $this->bannerModel = new AdvertisingBannerModel();
    }

    private function canManagePosts()
    {
        return SessionHelper::isLoggedIn() && in_array(SessionHelper::getRole(), ['admin', 'staff']);
    }

    // --- HÀM HỖ TRỢ UPLOAD FILE (ĐÃ SỬA LỖI GHOST IMAGE) ---
    private function handleMediaUpload($inputName, $postId)
    {
        // Kiểm tra xem có file nào được chọn không
        if (empty($_FILES[$inputName]['name'][0])) return;

        $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/public/posts/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        foreach ($_FILES[$inputName]['tmp_name'] as $index => $tempFile) {
            // QUAN TRỌNG: Kiểm tra xem file có thực sự được upload không
            if (is_uploaded_file($tempFile) && $_FILES[$inputName]['error'][$index] == UPLOAD_ERR_OK) {

                $fileNameOriginal = $_FILES[$inputName]['name'][$index];
                $fileSize = $_FILES[$inputName]['size'][$index];

                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $fileType = finfo_file($finfo, $tempFile);
                finfo_close($finfo);

                $isImage = strpos($fileType, 'image') !== false;
                $isVideo = strpos($fileType, 'video') !== false;

                if (!$isImage && !$isVideo) continue;
                if (($isImage && $fileSize > 20 * 1024 * 1024) || ($isVideo && $fileSize > 100 * 1024 * 1024)) continue;

                $newFileName = uniqid();
                $targetFile = '';
                $dbType = 'image';

                if ($isVideo) {
                    $ext = pathinfo($fileNameOriginal, PATHINFO_EXTENSION);
                    $newFileName .= '.' . $ext;
                    $targetFile = $upload_dir . $newFileName;
                    if (move_uploaded_file($tempFile, $targetFile)) {
                        $dbType = 'video';
                    }
                } else {
                    // Xử lý ảnh
                    $targetFile = $upload_dir . $newFileName . '.png';
                    $img = null;
                    switch ($fileType) {
                        case 'image/jpeg':
                            $img = @imagecreatefromjpeg($tempFile);
                            break;
                        case 'image/png':
                            $img = @imagecreatefrompng($tempFile);
                            break;
                        case 'image/gif':
                            $img = @imagecreatefromgif($tempFile);
                            break;
                        case 'image/webp':
                            $img = @imagecreatefromwebp($tempFile);
                            break;
                    }
                    if ($img) {
                        imagepng($img, $targetFile, 9);
                        imagedestroy($img);
                    } else {
                        $ext = pathinfo($fileNameOriginal, PATHINFO_EXTENSION);
                        $targetFile = $upload_dir . $newFileName . '.' . $ext;
                        move_uploaded_file($tempFile, $targetFile);
                    }
                    $dbType = 'image';
                }

                if (file_exists($targetFile)) {
                    $image_path = str_replace($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/', '', $targetFile);
                    $this->postModel->addPostImage($postId, $image_path, $dbType);
                }
            }
        }
    }

    public function index()
    {
        $all_posts = $this->postModel->getAllPosts();
        $canManage = $this->canManagePosts();
        $currentUserId = SessionHelper::getUserId();
        $latestEvent = $this->eventModel->getActiveEvents();
        $banners = $this->bannerModel->getAllActiveBanners();

        $main_list_posts_data = [];
        $staff_posts_data = [];

        foreach ($all_posts as $post) {
            $author = $this->userModel->getUserById($post['author_id']);
            $post['author_avatar'] = $author['avatar'] ?? '/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg';
            $post['author_name'] = $author['fullname'] ?? $author['username'] ?? 'Người dùng';
            $post['author_role'] = $author['role'] ?? 'user';

            $post['reactions_summary'] = $this->postModel->getReactionsSummary($post['id']);
            $post['user_reaction'] = $currentUserId ? $this->postModel->getUserReaction($post['id'], $currentUserId) : null;

            $images = $this->postModel->getPostImages($post['id']);
            $post['thumbnail'] = !empty($images) ? $images[0]['image_path'] : '/webdacn_quanlyclb/public/uploads/default_thumbnail.jpg';

            $post['snippet'] = mb_substr(strip_tags($post['content']), 0, 100) . '...';

            if ($post['author_role'] === 'staff') {
                $staff_posts_data[] = $post;
            } else {
                $main_list_posts_data[] = $post;
            }
        }

        usort($main_list_posts_data, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        usort($staff_posts_data, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        include 'app/views/post/post.php';
    }

    public function detail($id)
    {
        $post = $this->postModel->getPostById($id);
        if (!$post) {
            $_SESSION['error'] = "Bài viết không tồn tại!";
            header("Location: /webdacn_quanlyclb");
            exit();
        }

        $author = $this->userModel->getUserById($post['author_id']);
        $post['author_avatar'] = $author['avatar'] ?? '/webdacn_quanlyclb/uploads/default_avatar.jpg';
        $post['author_name'] = $author['username'] ?? 'Người dùng';

        $images = $this->postModel->getPostImages($id);
        $comments = $this->postModel->getCommentsByPostId($id);
        $canManage = $this->canManagePosts();
        $currentUserId = SessionHelper::getUserId();

        $reactions_summary = $this->postModel->getReactionsSummary($id);

        $user_reaction = null;
        if ($currentUserId) {
            $user_reaction = $this->postModel->getUserReaction($id, $currentUserId);
        }

        // Lấy danh sách bài viết phụ
        $subPosts = $this->postModel->getSubPosts($id);
        foreach ($subPosts as &$sub) {
            $sub['images'] = $this->postModel->getPostImages($sub['id']);
        }
        unset($sub);

        include 'app/views/shares/header.php';
        include 'app/views/post/detail.php';
    }

    public function create()
    {
        if (!$this->canManagePosts()) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện hành động này!";
            header("Location: /webdacn_quanlyclb");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== SessionHelper::getCsrfToken()) {
                $_SESSION['error'] = "Yêu cầu không hợp lệ (CSRF token không khớp)!";
                header("Location: /webdacn_quanlyclb/default/create");
                exit();
            }

            $title = mb_strtoupper($_POST['title'] ?? '', 'UTF-8');
            $content = $_POST['content'] ?? '';
            $category = $_POST['category'] ?? 'Thông báo';
            $team_id = !empty($_POST['team_id']) ? (int) $_POST['team_id'] : null;
            $author_id = $_SESSION['user_id'];

            $allowedCategories = ['Thông báo', 'Sự kiện', 'Chiêu sinh'];
            if (!in_array($category, $allowedCategories)) {
                $_SESSION['error'] = "Thể loại không hợp lệ!";
                header("Location: /webdacn_quanlyclb/default/create");
                exit();
            }

            if (empty($title) || empty($content)) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ tiêu đề và nội dung!";
                header("Location: /webdacn_quanlyclb/default/create");
                exit();
            }

            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/public/posts/';
            if (!is_dir($upload_dir))
                mkdir($upload_dir, 0755, true);

            try {
                // 1. TẠO BÀI VIẾT CHÍNH
                $post_id = $this->postModel->addPost($title, $content, $category, $author_id, $team_id, null);
                if (!$post_id)
                    throw new Exception("Không thể thêm bài viết vào cơ sở dữ liệu!");

                // 2. UPLOAD THUMBNAIL (BÀI CHÍNH) - LUÔN LÀ ẢNH
                if (!empty($_FILES['thumbnail']['name'])) {
                    $tempFile = $_FILES['thumbnail']['tmp_name'];
                    if (exif_imagetype($tempFile)) {
                        $newFileName = uniqid() . '_thumb.png';
                        $targetFile = $upload_dir . $newFileName;
                        if (move_uploaded_file($tempFile, $targetFile)) {
                            $image_path = str_replace($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/', '', $targetFile);
                            // Thumbnail luôn là image
                            $this->postModel->addPostImage($post_id, $image_path, 'image');
                        }
                    } else {
                        throw new Exception("Ảnh đại diện không hợp lệ!");
                    }
                } else {
                    throw new Exception("Thiếu ảnh đại diện!");
                }

                // 3. UPLOAD MEDIA (BÀI CHÍNH) - CÓ THỂ LÀ VIDEO HOẶC ẢNH
                $this->handleMediaUpload('content_media', $post_id);

                // 4. XỬ LÝ BÀI VIẾT PHỤ (SUB-POSTS)
                if (!empty($_POST['sub_posts']) && is_array($_POST['sub_posts'])) {
                    foreach ($_POST['sub_posts'] as $index => $subData) {
                        $subTitle = $subData['title'] ?? '';
                        $subContent = $subData['content'] ?? '';

                        if (!empty($subTitle) && !empty($subContent)) {
                            $sub_id = $this->postModel->addPost($subTitle, $subContent, $category, $author_id, $team_id, $post_id);

                            $inputName = "sub_posts_media_" . $index;
                            $this->handleMediaUpload($inputName, $sub_id);
                        }
                    }
                }

                $_SESSION['message'] = "Thêm bài viết thành công!";
                header("Location: /webdacn_quanlyclb");
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = "Thêm bài viết thất bại: " . $e->getMessage();
                header("Location: /webdacn_quanlyclb/default/create");
                exit();
            }
        }

        include 'app/views/shares/header.php';
        include 'app/views/post/create.php';
        include 'app/views/shares/footer.php';
    }

    public function edit($id)
    {
        if (!$this->canManagePosts()) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện hành động này!";
            header("Location: /webdacn_quanlyclb");
            exit();
        }

        $post = $this->postModel->getPostById($id);
        if (!$post) {
            $_SESSION['error'] = "Bài viết không tồn tại!";
            header("Location: /webdacn_quanlyclb");
            exit();
        }

        $currentUserId = SessionHelper::getUserId();
        $currentUserRole = SessionHelper::getRole();
        $isAuthor = ($currentUserId == $post['author_id']);

        if ($currentUserRole === 'staff') {
            if (!$isAuthor) {
                $_SESSION['error'] = "Bạn không có quyền sửa bài viết này!";
                header("Location: /webdacn_quanlyclb");
                exit();
            }
            if (!empty($post['team_id'])) {
                if (!SessionHelper::isClubManager($post['team_id'], $this->db)) {
                    $_SESSION['error'] = "Bạn không có quyền sửa bài viết của CLB này!";
                    header("Location: /webdacn_quanlyclb");
                    exit();
                }
            }
        } elseif ($currentUserRole !== 'admin') {
            $_SESSION['error'] = "Bạn không có quyền sửa bài viết này!";
            header("Location: /webdacn_quanlyclb");
            exit();
        }

        require_once __DIR__ . '/../models/TeamModel.php';
        $teamModel = new TeamModel($this->db);
        $availableClubs = ($currentUserRole === 'admin') ? $teamModel->getAllTeams() : SessionHelper::getManagedClubs($this->db);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== SessionHelper::getCsrfToken()) {
                $_SESSION['error'] = "CSRF Token không hợp lệ!";
                header("Location: /webdacn_quanlyclb/default/edit/$id");
                exit();
            }

            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $category = $_POST['category'] ?? 'Thông báo';
            $team_id = !empty($_POST['team_id']) ? (int) $_POST['team_id'] : null;

            if (empty($title) || empty($content)) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ tiêu đề và nội dung!";
                header("Location: /webdacn_quanlyclb/default/edit/$id");
                exit();
            }

            try {
                // 1. CẬP NHẬT BÀI CHÍNH
                $this->postModel->updatePost($id, $title, $content, $category, $team_id);

                // Upload ảnh mới cho bài chính (Sử dụng hàm đã sửa để lưu đúng type)
                $this->handleMediaUpload('images', $id);

                // Xóa ảnh cũ
                if (!empty($_POST['delete_images'])) {
                    foreach ($_POST['delete_images'] as $image_id) {
                        $image = $this->postModel->getPostImageById($image_id);
                        if ($image) {
                            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/' . $image['image_path'];
                            if (file_exists($file_path))
                                unlink($file_path);
                            $this->postModel->deletePostImage($image_id);
                        }
                    }
                }

                // 2. CẬP NHẬT CÁC BÀI VIẾT PHỤ ĐÃ CÓ
                if (!empty($_POST['existing_subs']) && is_array($_POST['existing_subs'])) {
                    foreach ($_POST['existing_subs'] as $subId => $subData) {
                        if (!empty($_POST['delete_sub_posts']) && in_array($subId, $_POST['delete_sub_posts'])) {
                            $this->postModel->deletePost($subId, $currentUserId, true);
                            continue;
                        }

                        $subTitle = $subData['title'] ?? '';
                        $subContent = $subData['content'] ?? '';
                        if ($subTitle && $subContent) {
                            $this->postModel->updatePost($subId, $subTitle, $subContent, $category, $team_id);
                            // Upload media cho bài phụ (Sử dụng hàm đã sửa)
                            $this->handleMediaUpload("existing_sub_media_$subId", $subId);
                        }
                    }
                }

                // Xóa ảnh lẻ của các bài phụ
                if (!empty($_POST['delete_sub_images'])) {
                    foreach ($_POST['delete_sub_images'] as $sImgId) {
                        $image = $this->postModel->getPostImageById($sImgId);
                        if ($image) {
                            $file_path = $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/' . $image['image_path'];
                            if (file_exists($file_path))
                                unlink($file_path);
                            $this->postModel->deletePostImage($sImgId);
                        }
                    }
                }

                // 3. THÊM BÀI VIẾT PHỤ MỚI
                if (!empty($_POST['sub_posts']) && is_array($_POST['sub_posts'])) {
                    foreach ($_POST['sub_posts'] as $index => $newSubData) {
                        $newTitle = $newSubData['title'] ?? '';
                        $newContent = $newSubData['content'] ?? '';

                        if (!empty($newTitle) && !empty($newContent)) {
                            $newSubId = $this->postModel->addPost($newTitle, $newContent, $category, $post['author_id'], $team_id, $id);
                            // Upload media cho bài phụ mới (Sử dụng hàm đã sửa)
                            $this->handleMediaUpload("sub_posts_media_$index", $newSubId);
                        }
                    }
                }

                $_SESSION['message'] = "Bài viết đã được cập nhật thành công!";
                header("Location: /webdacn_quanlyclb/default/detail/$id");
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = "Cập nhật thất bại: " . $e->getMessage();
                header("Location: /webdacn_quanlyclb/default/edit/$id");
                exit();
            }
        }

        $images = $this->postModel->getPostImages($id);

        $subPosts = $this->postModel->getSubPosts($id);
        foreach ($subPosts as &$sub) {
            $sub['images'] = $this->postModel->getPostImages($sub['id']);
        }
        unset($sub);

        include 'app/views/shares/header.php';
        include 'app/views/post/edit.php';
        include 'app/views/shares/footer.php';
    }

    public function delete($id)
    {
        if (!SessionHelper::isLoggedIn()) {
            $_SESSION['error'] = "Vui lòng đăng nhập!";
            header("Location: /webdacn_quanlyclb");
            exit();
        }

        $user_id = SessionHelper::getUserId();
        $is_admin = SessionHelper::isAdmin();

        if ($this->postModel->deletePost($id, $user_id, $is_admin)) {
            $_SESSION['success'] = "Xóa bài viết thành công!";
        } else {
            $_SESSION['error'] = "Bạn không có quyền xóa bài viết này!";
        }

        header("Location: /webdacn_quanlyclb");
        exit();
    }

    public function comment($post_id)
    {
        SessionHelper::requireLogin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = $_POST['content'] ?? '';
            $user_id = $_SESSION['user_id'];
            if (!empty($content)) {
                $this->postModel->addComment($post_id, $user_id, $content);
            }
        }
        header("Location: /webdacn_quanlyclb/default/detail/$post_id");
        exit();
    }

    public function deleteComment($comment_id)
    {
        if (!$this->canManagePosts()) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện hành động này!";
            header("Location: /webdacn_quanlyclb");
            exit();
        }

        $comment = $this->postModel->getCommentById($comment_id);
        if (!$comment) {
            $_SESSION['error'] = "Bình luận không tồn tại!";
            header("Location: /webdacn_quanlyclb/default/detail/" . $_GET['post_id']);
            exit();
        }

        $currentUserId = $_SESSION['user_id'];
        $isAdmin = SessionHelper::isAdmin();

        if ($isAdmin || $currentUserId == $comment['user_id']) {
            $this->postModel->deleteComment($comment_id);
            $_SESSION['success'] = "Xóa bình luận thành công!";
        } else {
            $_SESSION['error'] = "Bạn không có quyền xóa bình luận này!";
        }

        header("Location: /webdacn_quanlyclb/default/detail/" . $_GET['post_id']);
        exit();
    }

    public function react()
    {
        if (!SessionHelper::isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập để thực hiện.']);
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
            exit();
        }

        header('Content-Type: application/json');
        $post_id = $_POST['post_id'] ?? null;
        $reaction_type = $_POST['reaction_type'] ?? null;
        $user_id = SessionHelper::getUserId();

        if (!$post_id || !$reaction_type) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ.']);
            exit();
        }

        $allowed_reactions = ['like', 'love', 'haha', 'wow', 'sad', 'angry'];
        if (!in_array($reaction_type, $allowed_reactions)) {
            echo json_encode(['success' => false, 'message' => 'Loại cảm xúc không hợp lệ.']);
            exit();
        }

        try {
            $existing_reaction = $this->postModel->getUserReaction($post_id, $user_id);
            if ($existing_reaction && $existing_reaction === $reaction_type) {
                $this->postModel->removeReaction($post_id, $user_id);
                $action = 'removed';
            } else {
                $this->postModel->addOrUpdateReaction($post_id, $user_id, $reaction_type);
                $action = 'added_or_updated';
            }

            $new_summary = $this->postModel->getReactionsSummary($post_id);
            $my_new_reaction = ($action == 'removed') ? null : $reaction_type;

            echo json_encode([
                'success' => true,
                'action' => $action,
                'new_summary' => $new_summary,
                'my_reaction' => $my_new_reaction
            ]);
            exit();
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Lỗi máy chủ: ' . $e->getMessage()]);
            exit();
        }
    }

    // === HÀM GỌI GROQ API (MIỄN PHÍ) ===
    public function generateAI()
    {
        // 1. Kiểm tra quyền
        if (!SessionHelper::isLoggedIn() || !in_array(SessionHelper::getRole(), ['admin', 'staff'])) {
            echo json_encode(['success' => false, 'message' => 'Không có quyền truy cập']);
            exit;
        }

        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true);
        $title = $input['prompt'] ?? '';

        if (empty($title)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập nội dung yêu cầu']);
            exit;
        }

        $configFile = APP_ROOT . '/app/config/config.php';
        $apiKey = '';
        if (file_exists($configFile)) {
            $config = require $configFile;
            $apiKey = $config['api']['groq_key'] ?? '';
        }

        if (empty($apiKey) || $apiKey === 'YOUR_GROQ_API_KEY_HERE') {
            echo json_encode(['success' => false, 'message' => 'Lỗi Server: Chưa cấu hình API Key.']);
            exit;
        }

        // 3. Tạo nội dung gửi đi (Prompt)
        $promptText = "Bạn là trợ lý truyền thông cho CLB sinh viên HUTECH. Hãy viết nội dung bài viết dựa trên yêu cầu sau: '$title'. 

Yêu cầu:
- Nội dung chia đoạn rõ ràng
- Giọng văn trẻ trung, phù hợp với sinh viên
- Có thể dùng HTML cơ bản (thẻ <p>, <br>, <b>, <ul>, <li>) để định dạng
- Độ dài khoảng 300-500 từ
- Tập trung vào thông tin chính, hấp dẫn người đọc";

        $data = [
            "model" => "", // Model miễn phí của Groq
            "messages" => [
                [
                    "role" => "system",
                    "content" => "Bạn là trợ lý AI chuyên viết nội dung cho các câu lạc bộ sinh viên HUTECH. Hãy viết nội dung sinh động, hấp dẫn và phù hợp với giới trẻ. Luôn trả lời bằng Tiếng Việt."
                ],
                [
                    "role" => "user",
                    "content" => $promptText
                ]
            ],
            "temperature" => 0.7,
            "max_tokens" => 1500,
            "stream" => false
        ];

        // 4. Gửi Request đến Groq API
        $ch = curl_init("https://api.groq.com/openai/v1/chat/completions"); //html của groq
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            echo json_encode(['success' => false, 'message' => 'Lỗi kết nối: ' . curl_error($ch)]);
            curl_close($ch);
            exit;
        }
        curl_close($ch);

        // 5. Xử lý kết quả trả về
        $result = json_decode($response, true);

        if ($httpCode !== 200) {
            $errorMsg = $result['error']['message'] ?? 'Lỗi không xác định từ API';
            echo json_encode(['success' => false, 'message' => "Lỗi API ($httpCode): " . $errorMsg]);
            exit;
        }

        if (isset($result['choices'][0]['message']['content'])) {
            $aiText = $result['choices'][0]['message']['content'];
            echo json_encode(['success' => true, 'content' => $aiText]);
        } else {
            echo json_encode(['success' => false, 'message' => 'API không trả về nội dung.']);
        }
        exit;
    }
}
