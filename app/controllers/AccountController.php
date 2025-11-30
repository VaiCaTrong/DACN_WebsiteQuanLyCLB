<?php
require_once 'app/config/database.php';
require_once 'app/models/AccountModel.php';
require_once 'app/helpers/SessionHelper.php';
require_once('app/utils/JWTHandler.php');

class AccountController
{
    private $accountModel;
    private $db;
    private $jwtHandler;

    public function __construct()
{

    $this->accountModel = new AccountModel($this->db);
    $this->jwtHandler = new JWTHandler();
}

    public function register()
    {
        SessionHelper::start();
        include_once 'app/views/account/register.php';
    }

    // Cập nhật phương thức login để kiểm tra trạng thái tài khoản
    public function login()
    {
        SessionHelper::start();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $account = $this->accountModel->getAccountByUsername($username);

            if ($account && password_verify($password, $account->password)) {
                $_SESSION['user_id'] = $account->id;
                $_SESSION['username'] = $account->username;
                $_SESSION['role'] = $account->role;

                // Xác định redirect URL an toàn
                $redirectUrl = '/webdacn_quanlyclb';
                if (isset($_SESSION['redirect_url']) && strpos($_SESSION['redirect_url'], '/webdacn_quanlyclb') === 0) {
                    $redirectUrl = $_SESSION['redirect_url'];
                    unset($_SESSION['redirect_url']);
                }

                if ($account->status === 'disabled') {
                    // Lưu thông tin user vào session tạm thời để hiển thị trong trang disable
                    $_SESSION['disabled_user'] = [
                        'id' => $account->id,
                        'username' => $account->username,
                        'disable_reason' => $account->disable_reason
                    ];
                    header('Location: /webdacn_quanlyclb/account/disabled');
                    exit();
                }

                header("Location: $redirectUrl");
                exit;
            } else {
                $error = $account ? "Mật khẩu không đúng!" : "Không tìm thấy tài khoản!";
                include_once 'app/views/account/login.php';
                exit;
            }
        }
        include_once 'app/views/account/login.php';
    }


    public function save()
    {
        SessionHelper::start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $fullName = $_POST['fullname'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmpassword'] ?? '';
            $role = $_POST['role'] ?? 'user';
            $email = $_POST['email'] ?? null;
            $phone = $_POST['phone'] ?? null;
            $errors = [];

            // Validate các trường bắt buộc
            if (empty($username)) $errors['username'] = "Vui lòng nhập username!";
            if (empty($fullName)) $errors['fullname'] = "Vui lòng nhập fullname!";
            if (empty($password)) $errors['password'] = "Vui lòng nhập password!";
            if ($password != $confirmPassword) $errors['confirmPass'] = "Mật khẩu và xác nhận chưa khớp!";
            // Thêm sau phần validate
            $existingUser = $this->accountModel->getAccountByUsername($username);
            if ($existingUser) {
                $errors['username'] = "Tên đăng nhập đã tồn tại!";
            }

            if (!empty($email)) {
                $existingEmail = $this->accountModel->getAccountByEmail($email);
                if ($existingEmail) {
                    $errors['email'] = "Email đã được sử dụng!";
                }
            }
            // Xử lý upload avatar
            $avatarPath = null;
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
                $targetDir = "public/uploads/avatars/";
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $fileName = uniqid() . '.png'; // Luôn lưu dưới dạng .png
                $targetFile = $targetDir . $fileName;
                $tempFile = $_FILES['avatar']['tmp_name'];

                // Kiểm tra file có phải là ảnh
                $imageInfo = getimagesize($tempFile);
                if ($imageInfo === false) {
                    $errors['avatar'] = "File không phải là hình ảnh!";
                } elseif ($_FILES['avatar']['size'] > 20000000) { // Giới hạn 20MB
                    $errors['avatar'] = "Kích thước ảnh quá lớn (tối đa 20MB)!";
                } else {
                    // Xác định loại ảnh bằng exif_imagetype
                    $imageType = exif_imagetype($tempFile);
                    $allowedTypes = [
                        IMAGETYPE_JPEG,
                        IMAGETYPE_PNG,
                        IMAGETYPE_GIF,
                        IMAGETYPE_BMP,
                        IMAGETYPE_WEBP,
                    ];

                    if (!in_array($imageType, $allowedTypes)) {
                        $errors['avatar'] = "Định dạng ảnh không được hỗ trợ! (Chỉ hỗ trợ JPG, PNG, GIF, BMP, WebP)";
                    } else {
                        // Upload file tạm thời
                        if (move_uploaded_file($tempFile, $targetFile . '.' . image_type_to_extension($imageType, false))) {
                            $tempTarget = $targetFile . '.' . image_type_to_extension($imageType, false);
                            // Convert sang PNG bằng GD
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
                                // Lưu dưới dạng PNG (nén chất lượng 9 để giảm size)
                                imagepng($img, $targetFile, 9);
                                imagedestroy($img);
                                unlink($tempTarget); // Xóa file tạm gốc
                                $avatarPath = $targetFile;
                            } else {
                                $errors['avatar'] = "Không thể convert ảnh sang PNG!";
                                unlink($tempTarget); // Xóa nếu thất bại
                            }
                        } else {
                            $errors['avatar'] = "Có lỗi khi upload ảnh!";
                        }
                    }
                }
            }

            if (count($errors) > 0) {
                include_once 'app/views/account/register.php';
                return;
            }

            // Hash mật khẩu trước khi lưu
            $passwordHashed = password_hash($password, PASSWORD_BCRYPT);

            $result = $this->accountModel->save(
                $username,
                $fullName,
                $passwordHashed,
                $role,
                $email,
                $phone,
                $avatarPath
            );

            if ($result) {
                $_SESSION['register_success'] = "Đăng ký tài khoản $username thành công!";
                header("Location: /webdacn_quanlyclb/account/login");
                exit;
            } else {
                $errors['general'] = "Đăng ký tài khoản không thành công!";
                include_once 'app/views/account/register.php';
            }
        }
    }

    public function logout()
    {
        SessionHelper::logout();
    }

    public function index()
    {
        SessionHelper::start();
        if (!SessionHelper::isAdmin()) {
            header('Location: /webdacn_quanlyclb');
            exit;
        }

        $accounts = $this->accountModel->getAllAccounts();
        include_once 'app/views/account/index.php';
    }

    public function edit()
    {
        $user_id = SessionHelper::getUserId();
        $account = $this->accountModel->getAccountById($user_id);
        $errors = [];

        if (!$account) {
            $_SESSION['error'] = "Tài khoản không tồn tại.";
            header('Location: /webdacn_quanlyclb/account/edit');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? $account['username'];
            $fullName = $_POST['fullname'] ?? $account['fullname'];
            $role = $_POST['role'] ?? $account['role'];
            $email = $_POST['email'] ?? $account['email'];
            $phone = $_POST['phone'] ?? $account['phone'];
            $password = $_POST['password'] ?? $account['password']; // Giữ nguyên nếu không đổi
            $passwordHashed = $password; // Mặc định giữ nguyên password

            // Validate các trường bắt buộc
            if (empty($username)) $errors['username'] = "Vui lòng nhập username!";
            if (empty($fullName)) $errors['fullname'] = "Vui lòng nhập fullname!";
            if (!empty($_POST['password']) && $_POST['password'] !== $_POST['confirm_password']) {
                $errors['confirmPass'] = "Mật khẩu và xác nhận chưa khớp!";
            } elseif (!empty($_POST['password'])) {
                $passwordHashed = password_hash($_POST['password'], PASSWORD_BCRYPT);
            }

            // Xử lý upload avatar
            $avatarPath = $account['avatar']; // Giữ nguyên avatar cũ nếu không upload mới
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
                $targetDir = "public/uploads/avatars/";
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $fileName = uniqid() . '.png'; // Luôn lưu dưới dạng .png
                $targetFile = $targetDir . $fileName;
                $tempFile = $_FILES['avatar']['tmp_name'];

                // Kiểm tra file có phải là ảnh
                $imageInfo = getimagesize($tempFile);
                if ($imageInfo === false) {
                    $errors['avatar'] = "File không phải là hình ảnh!";
                } elseif ($_FILES['avatar']['size'] > 20000000) { // Giới hạn 20MB
                    $errors['avatar'] = "Kích thước ảnh quá lớn (tối đa 20MB)!";
                } else {
                    // Xác định loại ảnh bằng exif_imagetype
                    $imageType = exif_imagetype($tempFile);
                    $allowedTypes = [
                        IMAGETYPE_JPEG,
                        IMAGETYPE_PNG,
                        IMAGETYPE_GIF,
                        IMAGETYPE_BMP,
                        IMAGETYPE_WEBP,
                    ];

                    if (!in_array($imageType, $allowedTypes)) {
                        $errors['avatar'] = "Định dạng ảnh không được hỗ trợ! (Chỉ hỗ trợ JPG, PNG, GIF, BMP, WebP)";
                    } else {
                        // Upload file tạm thời
                        if (move_uploaded_file($tempFile, $targetFile . '.' . image_type_to_extension($imageType, false))) {
                            $tempTarget = $targetFile . '.' . image_type_to_extension($imageType, false);
                            // Convert sang PNG bằng GD
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
                                // Lưu dưới dạng PNG (nén chất lượng 9 để giảm size)
                                imagepng($img, $targetFile, 9);
                                imagedestroy($img);
                                unlink($tempTarget); // Xóa file tạm gốc
                                $avatarPath = $targetFile;
                            } else {
                                $errors['avatar'] = "Không thể convert ảnh sang PNG!";
                                unlink($tempTarget); // Xóa nếu thất bại
                            }
                        } else {
                            $errors['avatar'] = "Có lỗi khi upload ảnh!";
                        }
                    }
                }
            }

            if (count($errors) > 0) {
                include_once 'app/views/account/edit.php';
                return;
            }

            // Cập nhật tài khoản
            $success = $this->accountModel->update($user_id, $username, $fullName, $passwordHashed, $role, $email, $phone, $avatarPath);

            if ($success) {
                $_SESSION['message'] = "Cập nhật tài khoản thành công!";
                header("Location: /webdacn_quanlyclb/account");
                exit;
            } else {
                $errors['general'] = "Cập nhật tài khoản không thành công!";
                include_once 'app/views/account/edit.php';
            }
        }

        include_once 'app/views/account/edit.php';
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

    public function profile()
    {
        $user_id = SessionHelper::getUserId();
        $account = $this->accountModel->getAccountById($user_id);

        if (!$account) {
            echo "Tài khoản không tồn tại.";
            exit;
        }

        include 'app/views/account/profile.php';
    }

    public function update($id)
    {
        SessionHelper::start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $fullName = $_POST['fullname'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'user';
            $email = $_POST['email'] ?? null;
            $phone = $_POST['phone'] ?? null;
            $errors = [];

            $oldAccount = $this->accountModel->getAccountById($id);
            if (!$oldAccount) {
                $_SESSION['error'] = "Không tìm thấy tài khoản!";
                header('Location: /webdacn_quanlyclb/account');
                exit;
            }

            $avatar = $oldAccount->avatar;
            if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
                $targetDir = "public/uploads/avatars/";
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                $fileName = uniqid() . '_' . basename($_FILES['avatar']['name']);
                $targetFile = $targetDir . $fileName;
                $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                $check = getimagesize($_FILES['avatar']['tmp_name']);
                if ($check === false) {
                    $errors['avatar'] = "File không phải là hình ảnh!";
                }
                if ($_FILES['avatar']['size'] > 2000000) {
                    $errors['avatar'] = "Kích thước ảnh quá lớn (tối đa 2MB)!";
                }
                $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
                if (!in_array($imageFileType, $allowedTypes)) {
                    $errors['avatar'] = "Chỉ chấp nhận file JPG, JPEG, PNG & GIF.";
                }
                if (empty($errors)) {
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $targetFile)) {
                        if ($oldAccount->avatar && file_exists($oldAccount->avatar)) {
                            unlink($oldAccount->avatar);
                        }
                        $avatar = $targetFile;
                    } else {
                        $errors['avatar'] = "Lỗi khi upload ảnh!";
                    }
                }
            }

            if (empty($password)) {
                $passwordHashed = $oldAccount->password;
            } else {
                $passwordHashed = password_hash($password, PASSWORD_BCRYPT);
            }

            if (!empty($errors)) {
                $account = $oldAccount;
                include_once 'app/views/account/edit.php';
                return;
            }

            $success = $this->accountModel->update($id, $username, $fullName, $passwordHashed, $role, $email, $phone, $avatar);

            if ($success) {
                $_SESSION['message'] = "Cập nhật tài khoản thành công!";
                header("Location: /webdacn_quanlyclb/account");
                exit;
            } else {
                $errors['general'] = "Cập nhật tài khoản không thành công!";
                $account = $oldAccount;
                include_once 'app/views/account/edit.php';
            }
        }
    }

    public function delete($id)
    {
        if ($this->accountModel->delete($id)) {
            // Thành công, chuyển hướng hoặc thông báo
            $_SESSION['message'] = 'Xóa tài khoản thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi khi xóa tài khoản.';
        }
        header('Location: /webdacn_quanlyclb/Account'); // Điều chỉnh route phù hợp
        exit;
    }

    public function forgot_password()
    {
        SessionHelper::start();
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email'] ?? '');

            if (empty($email)) {
                $error = "Vui lòng nhập email!";
            } else {
                $account = $this->accountModel->getAccountByEmail($email);
                if (!$account) {
                    $error = "Email không tồn tại trong hệ thống!";
                } else {
                    header("Location: /webdacn_quanlyclb/account/reset_password?email=" . urlencode($email));
                    exit;
                }
            }
        }

        include_once 'app/views/account/forgot_password.php';
    }

    public function resetPassword()
    {
        SessionHelper::start();
        $email = $_GET['email'] ?? '';
        $error = '';

        $account = $this->accountModel->getAccountByEmail($email);
        if (!$account) {
            $_SESSION['error'] = "Email không hợp lệ!";
            header("Location: /webdacn_quanlyclb/account/forgot_password");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $newPassword = trim($_POST['password'] ?? '');
            $confirmPassword = trim($_POST['confirm_password'] ?? '');

            if (empty($newPassword)) {
                $error = "Vui lòng nhập mật khẩu mới!";
            } elseif ($newPassword !== $confirmPassword) {
                $error = "Mật khẩu xác nhận không khớp!";
            } elseif (strlen($newPassword) < 6) {
                $error = "Mật khẩu phải có ít nhất 6 ký tự!";
            } else {
                $passwordHashed = password_hash($newPassword, PASSWORD_BCRYPT);
                if ($this->accountModel->updatePassword($email, $passwordHashed)) {
                    $_SESSION['message'] = "Đặt lại mật khẩu thành công! Vui lòng đăng nhập với mật khẩu mới.";
                    header("Location: /webdacn_quanlyclb/account/login");
                    exit;
                } else {
                    $error = "Có lỗi xảy ra khi đặt lại mật khẩu!";
                }
            }
        }

        include_once 'app/views/account/reset_password.php';
    }

    public function reset_password()
    {
        $this->resetPassword();
    }

    public function checkLogin()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';
        $user = $this->accountModel->getAccountByUsername($username);
        if ($user && password_verify($password, $user->password)) {
            $_SESSION['username'] = $user->username;
            $_SESSION['role'] = $user->role;
            $token = $this->jwtHandler->encode(['id' => $user->id, 'username' => $user->username]);
            echo json_encode(['token' => $token, 'role' => $user->role]);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Invalid credentials']);
        }
    }

    public function notifications()
    {
        SessionHelper::requireLogin();
        $user_id = SessionHelper::getUserId();

        // Lấy danh liệu thông báo
        $notifications = $this->accountModel->getNotifications($user_id);

        // Truyền dữ liệu vào view
        require_once $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/account/notifications.php';
    }

    public function deleteNotification()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $notification_id = $data['notification_id'] ?? null;
            $user_id = $data['user_id'] ?? null;

            if ($notification_id && $user_id) {
                $result = $this->accountModel->deleteNotification($notification_id, $user_id);
                echo json_encode(['success' => $result]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
        }
        exit;
    }

    public function deleteAllNotifications()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $user_id = $data['user_id'] ?? null;

            if ($user_id) {
                $result = $this->accountModel->deleteAllNotifications($user_id);
                echo json_encode(['success' => $result]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
        }
        exit;
    }
    // Vô hiệu hóa tài khoản
    public function disable($id)
    {
        SessionHelper::requireLogin();

        if (!SessionHelper::isAdmin()) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện thao tác này!";
            header('Location: /webdacn_quanlyclb/account');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = $_POST['reason'] ?? '';

            if (empty($reason)) {
                $_SESSION['error'] = "Vui lòng nhập lý do vô hiệu hóa!";
                header("Location: /webdacn_quanlyclb/account");
                exit;
            }

            if ($this->accountModel->disableAccount($id, $reason)) {
                $_SESSION['message'] = "Đã vô hiệu hóa tài khoản thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi vô hiệu hóa tài khoản!";
            }

            header("Location: /webdacn_quanlyclb/account");
            exit;
        }
    }


    // Kích hoạt lại tài khoản
    public function enable($id)
    {
        SessionHelper::requireLogin();

        if (!SessionHelper::isAdmin()) {
            $_SESSION['error'] = "Bạn không có quyền thực hiện thao tác này!";
            header('Location: /webdacn_quanlyclb/account');
            exit;
        }

        if ($this->accountModel->enableAccount($id)) {
            $_SESSION['message'] = "Đã kích hoạt lại tài khoản thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi kích hoạt tài khoản!";
        }

        header("Location: /webdacn_quanlyclb/account");
        exit;
    }


    // Trang quản lý tài khoản cho admin (xem chi tiết)
    public function manage($id)
    {
        SessionHelper::requireLogin();

        if (!SessionHelper::isAdmin()) {
            header('Location: /webdacn_quanlyclb');
            exit;
        }

        $account = $this->accountModel->getAccountById($id);
        if (!$account) {
            $_SESSION['error'] = "Tài khoản không tồn tại!";
            header('Location: /webdacn_quanlyclb/account');
            exit;
        }

        $userTeams = $this->accountModel->getUserTeams($id);
        $leaderTeams = $this->accountModel->getUserLeaderTeams($id);
        $reactivationRequests = $this->accountModel->getReactivationRequests($id);

        include_once 'app/views/account/manage.php';
    }

    public function disabled()
    {
        SessionHelper::start();

        if (!isset($_SESSION['disabled_user'])) {
            header('Location: /webdacn_quanlyclb/account/login');
            exit();
        }

        include_once 'app/views/account/disabled.php';
    }

    public function requestReactivation()
    {
        SessionHelper::start();

        if (!isset($_SESSION['disabled_user'])) {
            header('Location: /webdacn_quanlyclb/account/login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = $_POST['reason'] ?? '';
            $user_id = $_SESSION['disabled_user']['id'];

            if (empty($reason)) {
                $_SESSION['error'] = "Vui lòng nhập lý do yêu cầu mở lại tài khoản!";
                header('Location: /webdacn_quanlyclb/account/disabled');
                exit;
            }

            if ($this->accountModel->createReactivationRequest($user_id, $reason)) {
                $_SESSION['message'] = "Yêu cầu mở lại tài khoản đã được gửi thành công!";
            } else {
                $_SESSION['error'] = "Có lỗi xảy ra khi gửi yêu cầu!";
            }

            header('Location: /webdacn_quanlyclb/account/disabled');
            exit;
        }
    }

}
