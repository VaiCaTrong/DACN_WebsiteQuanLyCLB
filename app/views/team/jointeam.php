<?php
// PHP to get data (Keep this)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$team_id = isset($_GET['team_id']) ? (int)$_GET['team_id'] : null;
$message = $_SESSION['message'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['message'], $_SESSION['error']);

// === STEP 1: INCLUDE HEADER ===
include $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/header.php';
?>

<style>
    :root {
        --primary: #E91E63;
        --primary-light: #FCE4EC;
        --primary-dark: #C2185B;
        --bg: #FFF9FB;
        --white: #fff;
        --success-bg: #E8F5E9;
        --success-border: #4CAF50;
        --error-bg: #FFEBEE;
        --error-border: #F44336;
        --text-color: #444;
        --shadow: 0 6px 20px rgba(233, 30, 99, 0.15);
    }

    /* Container wraps the form, provides centering and background */
    .join-form-page-wrapper { /* New class for the outer wrapper */
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 20px; /* Add padding */
        min-height: calc(100vh - 120px); /* Adjust based on header/footer height if needed */
        background-color: var(--bg); /* Set background for the whole area */
    }

    .join-form-container {
        max-width: 550px;
        width: 100%;
        padding: 2.5rem;
        background: var(--white);
        border-radius: 16px;
        box-shadow: var(--shadow);
        text-align: center;
        border-top: 5px solid var(--primary);
    }

    .join-form-container h2 {
        color: var(--primary-dark);
        margin-bottom: 2rem;
        font-weight: 600;
        font-size: 1.8em;
    }
     .join-form-container h2 i {
         margin-right: 10px;
         color: var(--primary);
     }

    .form-group {
        margin-bottom: 1.25rem;
        text-align: left;
    }

    .form-label {
        color: var(--primary-dark);
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.95em;
    }
     .form-label .text-danger {
         color: var(--error-border);
         margin-left: 2px;
     }

    .form-control {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-sizing: border-box;
        font-size: 1em;
        color: var(--text-color);
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .form-control:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 0.2rem var(--primary-light);
    }
     textarea.form-control {
         min-height: 100px;
         resize: vertical;
     }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
    }

    .btn-submit, .btn-cancel {
        padding: 0.7rem 1.8rem;
        border: none;
        border-radius: 50px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1em;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-submit {
        background: var(--primary);
        color: var(--white);
        box-shadow: 0 4px 10px rgba(233, 30, 99, 0.25);
    }
    .btn-submit:hover {
        background: var(--primary-dark);
        box-shadow: 0 6px 15px rgba(233, 30, 99, 0.35);
        transform: translateY(-2px);
    }

    .btn-cancel {
        background: #e9ecef;
        color: #495057;
    }
    .btn-cancel:hover {
        background: #dee2e6;
    }

    .message {
        margin-bottom: 1.5rem;
        padding: 1rem 1.25rem;
        border-radius: 8px;
        font-size: 1em;
        display: flex;
        align-items: center;
        gap: 10px;
        border-left-width: 5px;
        border-left-style: solid;
    }
     .message i {
         font-size: 1.3em;
     }
    .success {
        background-color: var(--success-bg);
        color: var(--success-border);
        border-color: var(--success-border);
    }
    .error {
        background-color: var(--error-bg);
        color: var(--error-border);
        border-color: var(--error-border);
    }

    /* Responsive */
    @media (max-width: 600px) {
        .join-form-page-wrapper {
             padding: 20px 10px; /* Reduce padding on mobile */
        }
        .join-form-container {
            padding: 1.5rem;
        }
        .join-form-container h2 {
            font-size: 1.5em;
        }
        .form-actions {
            flex-direction: column-reverse;
            gap: 10px;
        }
        .btn-submit, .btn-cancel {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="join-form-page-wrapper"> <div class="join-form-container">
        <h2><i class="fas fa-user-plus"></i>Yêu cầu tham gia Câu lạc bộ</h2>

        <?php if ($message): ?>
            <div class="message success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
            <div class="form-actions" style="justify-content: center;">
                 <a href="/webdacn_quanlyclb/Team" class="btn-cancel"><i class="fas fa-arrow-left"></i> Quay lại Danh sách</a>
            </div>
        <?php elseif ($error): ?>
            <div class="message error">
                <i class="fas fa-exclamation-triangle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
             <div class="form-actions" style="justify-content: center;">
                 <a href="/webdacn_quanlyclb/Team" class="btn-cancel"><i class="fas fa-arrow-left"></i> Quay lại Danh sách</a>
            </div>
        <?php elseif ($team_id): ?>
            <form method="POST" action="/webdacn_quanlyclb/Team/join?team_id=<?php echo htmlspecialchars($team_id); ?>">
                <input type="hidden" name="team_id" value="<?php echo htmlspecialchars($team_id); ?>">

                <div class="form-group">
                    <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required placeholder="Nhập họ tên đầy đủ của bạn">
                </div>
                <div class="row g-3">
                    <div class="col-md-6 form-group">
                        <label for="date_of_birth" class="form-label">Ngày sinh</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="khoa" class="form-label">Khoa</label>
                        <input type="text" class="form-control" id="khoa" name="khoa" placeholder="Ví dụ: Công nghệ thông tin">
                    </div>
                </div>
                <div class="form-group">
                    <label for="reason" class="form-label">Lý do muốn gia nhập <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="Tại sao bạn muốn tham gia CLB này?"></textarea>
                </div>
                <div class="form-group">
                    <label for="talent" class="form-label">Tài năng / Kỹ năng (nếu có)</label>
                    <input type="text" class="form-control" id="talent" name="talent" placeholder="Ví dụ: Chơi guitar, hát, thiết kế...">
                </div>

                <div class="form-actions">
                     <a href="/webdacn_quanlyclb/Team" class="btn-cancel"><i class="fas fa-times"></i> Hủy</a>
                    <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Gửi yêu cầu</button>
                </div>
            </form>
        <?php else: ?>
            <div class="message error">
                <i class="fas fa-exclamation-circle"></i>
                <span>Yêu cầu không hợp lệ hoặc thiếu thông tin Câu lạc bộ.</span>
            </div>
             <div class="form-actions" style="justify-content: center;">
                 <a href="/webdacn_quanlyclb/Team" class="btn-cancel"><i class="fas fa-arrow-left"></i> Quay lại Danh sách</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include $_SERVER['DOCUMENT_ROOT'] . '/webdacn_quanlyclb/app/views/shares/footer.php';
?>