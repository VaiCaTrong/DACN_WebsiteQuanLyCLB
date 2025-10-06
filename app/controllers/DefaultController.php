<?php
require_once __DIR__ . '/../models/PostModel.php';
require_once __DIR__ . '/../models/UserModel.php';
require_once 'app/helpers/SessionHelper.php';

class DefaultController
{
    private $postModel;
    private $userModel;

    public function __construct()
    {
        $this->postModel = new PostModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $posts = $this->postModel->getAllPosts();
        $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

        $uniquePosts = [];
        foreach ($posts as $post) {
            if (!isset($uniquePosts[$post['id']])) {
                $author = $this->userModel->getUserById($post['author_id']);
                $post['author_avatar'] = $author['avatar'] ?? '/webdacn_quanlyclb/uploads/default_avatar.jpg';
                $post['author_name'] = $author['username'] ?? 'Người dùng';
                $uniquePosts[$post['id']] = $post;
            }
        }
        $posts = array_values($uniquePosts);

        include 'app/views/shares/header.php';
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

        include 'app/views/shares/header.php';
        include 'app/views/post/detail.php';
    }

    public function create()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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

            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $team_id = !empty($_POST['team_id']) ? (int)$_POST['team_id'] : null;
            $author_id = $_SESSION['user_id'];

            if (empty($title) || empty($content)) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ tiêu đề và nội dung!";
                header("Location: /webdacn_quanlyclb/default/create");
                exit();
            }

            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/public/posts/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            try {
                $post_id = $this->postModel->addPost($title, $content, $author_id, $team_id);
                if (!$post_id) {
                    throw new Exception("Không thể thêm bài viết vào cơ sở dữ liệu!");
                }

                $errors = [];
                if (!empty($_FILES['images']['name'][0])) {
                    foreach ($_FILES['images']['tmp_name'] as $index => $tempFile) {
                        if ($_FILES['images']['error'][$index] == UPLOAD_ERR_OK) {
                            $imageInfo = getimagesize($tempFile);
                            if ($imageInfo === false) {
                                $errors[] = "File " . $_FILES['images']['name'][$index] . " không phải là ảnh!";
                                continue;
                            } elseif ($_FILES['images']['size'][$index] > 20000000) {
                                $errors[] = "File " . $_FILES['images']['name'][$index] . " vượt quá 20MB!";
                                continue;
                            }

                            $imageType = exif_imagetype($tempFile);
                            $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_BMP, IMAGETYPE_WEBP];
                            if (!in_array($imageType, $allowedTypes)) {
                                $errors[] = "File " . $_FILES['images']['name'][$index] . " có định dạng không được hỗ trợ!";
                                continue;
                            }

                            $fileName = uniqid() . '.png';
                            $targetFile = $upload_dir . $fileName;
                            $ext = image_type_to_extension($imageType, false);
                            if (move_uploaded_file($tempFile, $targetFile . '.' . $ext)) {
                                $tempTarget = $targetFile . '.' . $ext;
                                $img = null;
                                switch ($imageType) {
                                    case IMAGETYPE_JPEG:
                                        $img = imagecreatefromjpeg($tempTarget);
                                        break;
                                    case IMAGETYPE_PNG:
                                        $img = imagecreatefrompng($tempTarget);
                                        break;
                                    case IMAGETYPE_GIF:
                                        $img = imagecreatefromgif($tempTarget);
                                        break;
                                    case IMAGETYPE_BMP:
                                        $img = imagecreatefrombmp($tempTarget);
                                        break;
                                    case IMAGETYPE_WEBP:
                                        $img = imagecreatefromwebp($tempTarget);
                                        break;
                                }
                                if ($img) {
                                    imagepng($img, $targetFile, 9);
                                    imagedestroy($img);
                                    unlink($tempTarget);
                                    $image_path = str_replace($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/', '', $targetFile);
                                    $this->postModel->addPostImage($post_id, $image_path);
                                } else {
                                    unlink($tempTarget);
                                    $errors[] = "Lỗi khi chuyển đổi file " . $_FILES['images']['name'][$index] . " sang PNG!";
                                }
                            } else {
                                $errors[] = "Lỗi khi upload file " . $_FILES['images']['name'][$index] . "!";
                            }
                        } elseif ($_FILES['images']['error'][$index] !== UPLOAD_ERR_NO_FILE) {
                            $errors[] = "Lỗi upload file " . $_FILES['images']['name'][$index] . ": mã lỗi " . $_FILES['images']['error'][$index];
                        }
                    }
                }

                if (!empty($errors)) {
                    $_SESSION['error'] = implode("<br>", $errors);
                    header("Location: /webdacn_quanlyclb/default/create");
                    exit();
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
    }

    public function edit($id)
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $team_id = !empty($_POST['team_id']) ? (int)$_POST['team_id'] : null;

            if (empty($title) || empty($content)) {
                $_SESSION['error'] = "Vui lòng điền đầy đủ tiêu đề và nội dung!";
                header("Location: /webdacn_quanlyclb/default/edit/$id");
                exit();
            }

            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/public/posts/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            try {
                $this->postModel->updatePost($id, $title, $content, $team_id);

                $errors = [];
                if (!empty($_FILES['images']['name'][0])) {
                    foreach ($_FILES['images']['tmp_name'] as $index => $tempFile) {
                        if ($_FILES['images']['error'][$index] == UPLOAD_ERR_OK) {
                            $imageInfo = getimagesize($tempFile);
                            if ($imageInfo === false) {
                                $errors[] = "File " . $_FILES['images']['name'][$index] . " không phải là ảnh!";
                                continue;
                            } elseif ($_FILES['images']['size'][$index] > 20000000) {
                                $errors[] = "File " . $_FILES['images']['name'][$index] . " vượt quá 20MB!";
                                continue;
                            }

                            $imageType = exif_imagetype($tempFile);
                            $allowedTypes = [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_BMP, IMAGETYPE_WEBP];
                            if (!in_array($imageType, $allowedTypes)) {
                                $errors[] = "File " . $_FILES['images']['name'][$index] . " có định dạng không được hỗ trợ!";
                                continue;
                            }

                            $fileName = uniqid() . '.png';
                            $targetFile = $upload_dir . $fileName;
                            $ext = image_type_to_extension($imageType, false);
                            if (move_uploaded_file($tempFile, $targetFile . '.' . $ext)) {
                                $tempTarget = $targetFile . '.' . $ext;
                                $img = null;
                                switch ($imageType) {
                                    case IMAGETYPE_JPEG:
                                        $img = imagecreatefromjpeg($tempTarget);
                                        break;
                                    case IMAGETYPE_PNG:
                                        $img = imagecreatefrompng($tempTarget);
                                        break;
                                    case IMAGETYPE_GIF:
                                        $img = imagecreatefromgif($tempTarget);
                                        break;
                                    case IMAGETYPE_BMP:
                                        $img = imagecreatefrombmp($tempTarget);
                                        break;
                                    case IMAGETYPE_WEBP:
                                        $img = imagecreatefromwebp($tempTarget);
                                        break;
                                }
                                if ($img) {
                                    imagepng($img, $targetFile, 9);
                                    imagedestroy($img);
                                    unlink($tempTarget);
                                    $image_path = str_replace($_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/', '', $targetFile);
                                    $this->postModel->addPostImage($id, $image_path);
                                } else {
                                    unlink($tempTarget);
                                    $errors[] = "Lỗi khi chuyển đổi file " . $_FILES['images']['name'][$index] . " sang PNG!";
                                }
                            } else {
                                $errors[] = "Lỗi khi upload file " . $_FILES['images']['name'][$index] . "!";
                            }
                        } elseif ($_FILES['images']['error'][$index] !== UPLOAD_ERR_NO_FILE) {
                            $errors[] = "Lỗi upload file " . $_FILES['images']['name'][$index] . ": mã lỗi " . $_FILES['images']['error'][$index];
                        }
                    }
                }

                if (!empty($errors)) {
                    $_SESSION['error'] = implode("<br>", $errors);
                    header("Location: /webdacn_quanlyclb/default/edit/$id");
                    exit();
                }

                $_SESSION['message'] = "Bài viết đã được cập nhật!";
                header("Location: /webdacn_quanlyclb");
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = "Cập nhật bài viết thất bại: " . $e->getMessage();
                header("Location: /webdacn_quanlyclb/default/edit/$id");
                exit();
            }
        }

        $images = $this->postModel->getPostImages($id);

        include 'app/views/shares/header.php';
        include 'app/views/post/edit.php';
    }

    public function delete($id)
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
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

        $images = $this->postModel->getPostImages($id);
        if (is_array($images)) {
            foreach ($images as $image) {
                if (isset($image['image_path'])) {
                    $file_path = $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/' . $image['image_path'];
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
            }
        }

        $result = $this->postModel->deletePost($id);
        if ($result) {
            $_SESSION['message'] = "Bài viết đã được xóa thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi xóa bài viết!";
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
        if (!SessionHelper::isLoggedIn()) {
            $_SESSION['error'] = "Vui lòng đăng nhập để thực hiện hành động này!";
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
}