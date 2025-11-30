<?php
// CÁC INCLUDE GỐC
require_once 'app/models/TeamModel.php';
include 'app/views/shares/header.php';

// --- PHẦN ĐÃ SỬA ---

// Bổ sung 2 file này để có thể gọi AccountModel (giống như cách AccountController làm)
require_once 'app/config/database.php';
require_once 'app/models/AccountModel.php';

// Khởi tạo các đối tượng cần thiết
$db = (new Database())->getConnection();
$accountModel = new AccountModel($db);
$teamModel = new TeamModel(); // Vẫn giữ lại $teamModel gốc

// --- LOGIC MỚI ĐỂ LẤY THÔNG TIN TEAM CHÍNH XÁC ---
$team_name = 'Chưa tham gia';
$team_points = 0;

// Lấy danh sách các CLB user đã tham gia từ bảng 'user_team'
// thay vì đọc từ 'account.team_id'
$user_teams = $accountModel->getUserTeams($account['id']);

// Vì layout chỉ hỗ trợ 1 CLB, chúng ta sẽ lấy CLB đầu tiên
if (!empty($user_teams)) {
    $first_team = $user_teams[0];
    
    // Lấy tên CLB
    $team_name = htmlspecialchars($first_team['name'] ?? 'Đội không tồn tại');
    
    // Lấy điểm thành tích (đã có sẵn trong $first_team từ hàm getUserTeams)
    $team_points = htmlspecialchars($first_team['point'] ?? 0);
}
// --- KẾT THÚC PHẦN SỬA ---
?>

<style>
    /* CSS của bạn được giữ nguyên */
    :root {
        --primary: #E91E63;
        --primary-light: #FCE4EC;
        --primary-dark: #C2185B;
        --bg: #FFF9FB; /* Nền nhạt hơn */
        --white: #fff;
        --text-dark: #333;
        --text-medium: #000000ff;
        --border-color: #F8BBD0; /* Màu viền hồng nhạt */
        --shadow: 0 8px 25px rgba(233, 30, 99, 0.1);
    }

    /* Đổi body background */
    body {
        background: var(--bg);
    }

    .profile-container {
        max-width: 900px;
        margin-top: 2rem;
        margin-bottom: 2rem;
    }

    .profile-title {
        text-align: center;
        color: var(--primary);
        font-size: 2.2em;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 2rem;
    }
     .profile-title i {
         margin-right: 10px;
     }

    /* Cột trái (Ảnh và Nút) */
    .profile-sidebar {
        background: var(--white);
        border-radius: 16px;
        box-shadow: var(--shadow);
        padding: 2rem;
        text-align: center;
        border-top: 5px solid var(--primary);
        /* Thêm để giữ chiều cao */
        align-self: flex-start;
        position: sticky; /* Dính lại khi cuộn (nếu cột phải dài) */
        top: 80px; /* 60px header + 20px margin */
    }

    .profile-avatar-wrapper {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto 1.5rem auto;
    }
    .profile-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid var(--primary-light);
        box-shadow: 0 4px 15px rgba(233, 30, 99, 0.2);
    }
    /* Icon vai trò (admin, staff) */
    .profile-role-badge {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background-color: var(--primary);
        color: var(--white);
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        border: 2px solid var(--white);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    .profile-sidebar h4 {
        color: var(--text-dark);
        font-weight: 600;
        font-size: 1.4rem;
        margin-bottom: 0.25rem;
    }
    .profile-sidebar .text-muted {
        font-size: 1rem;
        color: var(--text-medium) !important;
    }

    .profile-actions {
        margin-top: 2rem;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    /* Nút bấm (CSS chung) */
    .btn-profile-action {
        display: block;
        width: 100%;
        padding: 12px 20px;
        border-radius: 50px;
        font-size: 1rem;
        font-weight: 500;
        text-decoration: none;
        border: 1px solid var(--primary);
        transition: all 0.3s ease;
        text-align: center;
    }
    /* Nút chính (Chỉnh sửa) */
    .btn-profile-action.btn-primary {
        background: var(--primary);
        color: var(--white);
    }
    .btn-profile-action.btn-primary:hover {
        background: var(--primary-dark);
        border-color: var(--primary-dark);
        box-shadow: 0 4px 12px rgba(233, 30, 99, 0.3);
        transform: translateY(-2px);
    }
    /* Nút phụ (CLB của tôi, Quản lý) */
    .btn-profile-action.btn-outline {
        background: var(--white);
        color: var(--primary);
    }
     .btn-profile-action.btn-outline:hover {
        background: var(--primary-light);
        color: var(--primary-dark);
        transform: translateY(-2px);
    }

    /* Cột phải (Thông tin) */
    .profile-details {
        background: var(--white);
        border-radius: 16px;
        box-shadow: var(--shadow);
        border-left: 5px solid var(--primary);
    }
    .profile-details .card-title {
        color: var(--primary-dark);
        font-size: 1.5em;
        font-weight: 700;
        margin-bottom: 20px;
        padding: 1.5rem 2rem 1rem 2rem; /* Thêm padding */
        border-bottom: 1px solid var(--primary-light);
        margin-bottom: 0; /* Xóa margin */
    }
    
    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .info-list li {
        display: flex;
        align-items: center;
        padding: 1.25rem 2rem; /* Tăng padding */
        border-bottom: 1px solid var(--primary-light);
        font-size: 1.05em;
    }
     .info-list li:last-child {
         border-bottom: none;
     }
     
    .info-list li i {
        color: var(--primary);
        font-size: 1.1em;
        width: 30px; /* Cố định độ rộng icon */
        text-align: center;
        margin-right: 15px;
    }
     .info-list li strong {
         color: var(--text-dark);
         font-weight: 600;
         width: 150px; /* Cố định độ rộng label */
         flex-shrink: 0;
     }
     .info-list li span {
         color: var(--text-medium);
         word-break: break-all; /* Chống tràn email dài */
     }
     /* Style cho điểm số */
     .info-list li .badge {
         font-size: 1.1em;
         padding: 0.5em 0.8em;
     }

    /* Responsive */
    @media (max-width: 767.98px) {
        .profile-sidebar {
            position: static; /* Bỏ sticky trên mobile */
            margin-bottom: 1.5rem;
        }
        .profile-actions {
            flex-direction: row; /* Nút nằm ngang */
            flex-wrap: wrap;
            justify-content: center;
        }
        .btn-profile-action {
            width: auto; /* Tự động co giãn */
        }
         .info-list li {
             flex-direction: column; /* Xếp chồng trên mobile */
             align-items: flex-start;
             gap: 5px;
             padding: 1rem 1.5rem;
         }
         .info-list li strong {
             width: auto; /* Bỏ cố định */
             color: var(--primary);
         }
         .info-list li i {
             display: none; /* Ẩn icon trên mobile cho gọn */
         }
    }
</style>

<div class="container profile-container">
    <h2 class="profile-title"><i class="fas fa-user-circle"></i> Thông tin cá nhân</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row g-4"> <div class="col-md-4">
            <div class="profile-sidebar">
                <div class="profile-avatar-wrapper">
                    <?php if ($account['avatar']): ?>
                        <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($account['avatar']); ?>" alt="Avatar" class="profile-avatar">
                    <?php else: ?>
                        <img src="/webdacn_quanlyclb/public/uploads/avatars/default_avatar.jpg" alt="Avatar" class="profile-avatar">
                    <?php endif; ?>
                    
                    <?php if ($account['role'] == 'admin'): ?>
                        <span class="profile-role-badge bg-danger" title="Quản trị viên"><i class="fas fa-shield-alt"></i></span>
                    <?php elseif ($account['role'] == 'staff'): ?>
                         <span class="profile-role-badge bg-warning" title="Chủ nhiệm CLB"><i class="fas fa-star"></i></span>
                    <?php endif; ?>
                </div>

                <h4 class="mb-0"><?php echo htmlspecialchars($account['fullname'] ?? $account['username']); ?></h4>
                <p class="text-muted">@<?php echo htmlspecialchars($account['username']); ?></p>

                <div class="profile-actions">
                    <a href="/webdacn_quanlyclb/account/edit" class="btn-profile-action btn-primary">
                        <i class="fas fa-edit me-2"></i>Chỉnh sửa
                    </a>
                    <?php if (SessionHelper::isUser()) : ?>
                        <a href="/webdacn_quanlyclb/team/myTeam" class="btn-profile-action btn-outline">
                            <i class="fas fa-users me-2"></i>CLB của tôi
                        </a>
                    <?php endif; ?>
                    <?php if (SessionHelper::isStaff()) : ?>
                        <a href="/webdacn_quanlyclb/Team/manageTeam" class="btn-profile-action btn-outline">
                            <i class="fas fa-cog me-2"></i>Quản lý CLB
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="profile-details">
                <div class="card-title">
                    <i class="fas fa-info-circle me-2"></i>Chi tiết tài khoản
                </div>
                <ul class="info-list">
                    <li>
                        <i class="fas fa-user-tag"></i>
                        <strong>Vai trò:</strong> 
                        <span>
                            <?php
                            $roleMap = [ 'admin' => 'Quản trị viên', 'staff' => 'Chủ nhiệm CLB', 'user' => 'Thành viên' ];
                            echo htmlspecialchars($roleMap[$account['role']] ?? 'Không xác định');
                            ?>
                        </span>
                    </li>
                    <li>
                        <i class="fas fa-envelope"></i>
                        <strong>Email:</strong> 
                        <span><?php echo htmlspecialchars($account['email'] ?? 'Chưa cập nhật'); ?></span>
                    </li>
                    <li>
                        <i class="fas fa-phone"></i>
                        <strong>Số điện thoại:</strong> 
                        <span><?php echo htmlspecialchars($account['phone'] ?? 'Chưa cập nhật'); ?></span>
                    </li>
                    <li>
                        <i class="fas fa-calendar-plus"></i>
                        <strong>Ngày tạo:</strong> 
                        <span><?php echo date('d/m/Y H:i', strtotime(htmlspecialchars($account['created_at']))); ?></span>
                    </li>
                    <li>
                        <i class="fas fa-calendar-check"></i>
                        <strong>Cập nhật lần cuối:</strong> 
                        <span><?php echo isset($account['updated_at']) ? date('d/m/Y H:i', strtotime(htmlspecialchars($account['updated_at']))) : 'Chưa cập nhật'; ?></span>
                    </li>
                </ul>
            </div>
            
            <div class="profile-details mt-4">
                 <div class="card-title">
                    <i class="fas fa-users-cog me-2"></i>Thông tin Câu lạc bộ
                </div>
                <ul class="info-list">
                    <li>
                        <i class="fas fa-user-friends"></i>
                        <strong>CLB Đã tham gia:</strong> 
                        <span><?php echo $team_name; // Đã escape ở trên ?></span>
                    </li>
                    <li>
                        <i class="fas fa-medal"></i>
                        <strong>Điểm thành tích:</strong> 
                        <span>
                            <span class="badge bg-success rounded-pill">
                                <?php echo $team_points; // Đã escape ở trên ?> điểm
                            </span>
                        </span>
                    </li>
                </ul>
            </div>
        </div>

    </div> </div> <?php include 'app/views/shares/footer.php'; ?>