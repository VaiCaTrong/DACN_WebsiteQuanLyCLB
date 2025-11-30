<style>
    :root {
        --primary: #E91E63;
        --primary-light: #FCE4EC; /* Nhạt hơn cho nền */
        --primary-dark: #C2185B;
        --bg: #FFF9FB; /* Nền body nhẹ nhàng hơn */
        --white: #fff;
        --success-bg: #E8F5E9; /* Xanh lá nhạt */
        --success-border: #4CAF50;
        --error-bg: #FFEBEE; /* Đỏ nhạt */
        --error-border: #F44336;
        --text-color: #444; /* Đậm hơn chút */
        --shadow-light: 0 4px 12px rgba(233, 30, 99, 0.08);
        --shadow-medium: 0 6px 20px rgba(233, 30, 99, 0.12);
    }

    /* Container chính */
    .important-day-container {
        max-width: 1000px; /* Rộng hơn chút */
        margin: 20px auto; /* Thêm margin top */
        padding: 2rem 2.5rem; /* Tăng padding */
        background: var(--white);
        border-radius: 16px; /* Bo tròn hơn */
        box-shadow: var(--shadow-medium);
    }

    /* Tiêu đề trang */
    .page-header {
        text-align: center;
        color: var(--primary);
        margin-bottom: 2rem; /* Tăng khoảng cách dưới */
        font-size: 2.4em; /* To hơn */
        font-weight: 600; /* Đậm vừa */
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--primary-light);
        padding-bottom: 1rem;
    }
     .page-header i {
        margin-right: 10px;
        vertical-align: middle; /* Căn icon đẹp hơn */
     }

    /* Khung thông tin User */
    .user-info-box { /* Đổi tên class */
        background: var(--primary-light);
        border-radius: 10px;
        padding: 1rem 1.5rem; /* Giảm padding chút */
        margin-bottom: 1.5rem; /* Giảm khoảng cách dưới */
        border-left: 5px solid var(--primary);
        color: var(--primary-dark);
        font-size: 1.05em;
    }
    .user-info-box strong {
        font-weight: 600;
    }

    /* Thông báo */
    .message-box { /* Đổi tên class */
        padding: 0.8rem 1.25rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        text-align: center;
        font-size: 1em;
        display: flex; /* Dùng flex để icon và text căn giữa */
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .message-box i {
        font-size: 1.2em;
    }
    .success-message {
        background: var(--success-bg);
        color: var(--success-border);
        border-left: 5px solid var(--success-border);
    }
    .error-message {
        background: var(--error-bg);
        color: var(--error-border);
        border-left: 5px solid var(--error-border);
    }

    /* Nút Thêm */
    .add-day-btn { /* Đổi tên class */
        display: block;
        width: fit-content;
        margin: 0 auto 2rem auto; /* Tăng khoảng cách dưới */
        background: var(--primary);
        color: var(--white);
        padding: 0.8rem 1.8rem; /* Điều chỉnh padding */
        border-radius: 50px; /* Bo tròn hoàn toàn */
        font-size: 1.1em;
        font-weight: 500; /* Mỏng hơn chút */
        text-decoration: none;
        box-shadow: var(--shadow-light);
        transition: all 0.3s ease;
        border: none;
    }
    .add-day-btn:hover {
        background: var(--primary-dark);
        box-shadow: var(--shadow-medium);
        transform: translateY(-2px);
    }
     .add-day-btn i {
         margin-right: 8px;
     }

    /* Danh sách ngày */
    .days-grid { /* Đổi tên class */
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); /* Responsive hơn */
        gap: 1.5rem; /* Khoảng cách vừa phải */
        padding: 0;
        margin: 0;
        list-style: none;
    }

    /* Từng mục ngày */
    .day-card { /* Đổi tên class */
        background: var(--white);
        border-radius: 12px;
        box-shadow: var(--shadow-light);
        padding: 1.5rem;
        border-left: 5px solid var(--primary);
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.3s ease;
    }
    .day-card:hover {
        box-shadow: var(--shadow-medium);
    }

    .day-card-header { /* Nhóm tiêu đề và ngày */
        margin-bottom: 0.8rem;
        border-bottom: 1px dashed var(--primary-light);
        padding-bottom: 0.8rem;
    }

    .day-card-title {
        color: var(--primary-dark);
        font-size: 1.3em;
        margin: 0 0 0.3rem 0; /* Giảm margin bottom */
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .day-card-title i {
        color: var(--primary);
        font-size: 1em; /* Nhỏ hơn chút */
    }

    .day-card-date {
        color: var(--primary);
        font-weight: 500; /* Mỏng hơn */
        display: block;
        font-size: 1em;
        display: flex;
        align-items: center;
        gap: 6px;
    }
     .day-card-date i {
         font-size: 0.9em;
     }

    .day-card-description {
        flex-grow: 1; /* Đẩy nút xuống dưới */
        margin-bottom: 1rem;
        color: var(--text-color);
        line-height: 1.6; /* Dãn dòng */
    }

    .day-card-actions { /* Đổi tên class */
        display: flex;
        gap: 10px;
        margin-top: auto; /* Đẩy xuống dưới cùng */
    }

    .day-card-actions a {
        color: var(--primary-dark);
        background: var(--primary-light);
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 0.9em;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .day-card-actions a:hover {
        background: var(--primary);
        color: var(--white);
        border-color: var(--primary-dark);
    }
    /* Style riêng cho nút xóa */
    .day-card-actions a.delete-link:hover {
        background: var(--error-border);
        border-color: #C62828;
    }

    /* Thông báo khi không có ngày nào */
    .no-days-message {
        text-align: center;
        color: #777;
        padding: 2rem;
        background-color: #f9f9f9;
        border-radius: 8px;
        border: 1px dashed #ddd;
    }
</style>

<div class="important-day-container">
    <h1 class="page-header"><i class="fas fa-calendar-heart"></i>Ngày Quan Trọng Của Bạn</h1>

    <div class="user-info-box">
        <i class="fas fa-user-circle me-2"></i>
        Xin chào, <strong><?php echo htmlspecialchars($_SESSION['username'] ?? 'Bạn'); ?></strong>! Hãy quản lý những ngày đặc biệt của mình nhé.
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message-box success-message">
            <i class="fas fa-check-circle"></i>
            <span><?php echo htmlspecialchars($_SESSION['message']); ?></span>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message-box error-message">
            <i class="fas fa-exclamation-triangle"></i>
            <span><?php echo htmlspecialchars($_SESSION['error']); ?></span>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <a href="/webdacn_quanlyclb/ImportantDay/add" class="add-day-btn">
        <i class="fas fa-plus"></i> Thêm Ngày Mới
    </a>

    <?php if (empty($days)): ?>
        <div class="no-days-message">
            <p><i class="fas fa-info-circle fa-2x mb-3 text-muted"></i></p>
            <p>Bạn chưa thêm ngày quan trọng nào.</p>
        </div>
    <?php else: ?>
        <ul class="days-grid">
            <?php foreach ($days as $day): ?>
                <li class="day-card">
                    <div class="day-card-header">
                        <h3 class="day-card-title">
                            <i class="fas fa-star text-warning"></i> <?php echo htmlspecialchars($day->title); ?>
                        </h3>
                        <span class="day-card-date">
                            <i class="fas fa-calendar-alt"></i>
                            <?php echo date('d/m/Y', strtotime(htmlspecialchars($day->date))); // Định dạng lại ngày ?>
                        </span>
                    </div>
                    <div class="day-card-description">
                        <?php echo nl2br(htmlspecialchars($day->description)); // Hiển thị xuống dòng ?>
                    </div>
                    <div class="day-card-actions">
                        <a href="/webdacn_quanlyclb/ImportantDay/edit/<?php echo $day->id; ?>"><i class="fas fa-edit"></i> Sửa</a>
                        <a href="/webdacn_quanlyclb/ImportantDay/delete/<?php echo $day->id; ?>" 
                           class="delete-link" 
                           onclick="return confirm('Bạn có chắc chắn muốn xóa ngày quan trọng \'<?php echo htmlspecialchars(addslashes($day->title)); ?>\'?');">
                           <i class="fas fa-trash-alt"></i> Xóa
                        </a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    
</div>