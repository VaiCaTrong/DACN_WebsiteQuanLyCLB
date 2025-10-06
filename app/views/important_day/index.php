<?php include 'app/views/shares/header.php'; ?>

<style>
    :root {
        --primary: #E91E63;
        --primary-light: #F8BBD0;
        --primary-dark: #C2185B;
        --bg: #FFF0F6;
        --white: #fff;
        --success: #FCE4EC;
        --success-border: #E91E63;
        --error: #FFEBEE;
        --error-border: #F44336;
    }

    body {
        background: var(--bg);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: var(--primary-dark);
        margin: 0;
        padding: 0;
        padding-top: 80px;
        /* Thêm padding-top để tránh header che, điều chỉnh dựa trên chiều cao header (70px + khoảng đệm) */
    }

    .container {
        max-width: 900px;
        margin: 0 auto 0 auto;
        background: var(--white);
        border-radius: 18px;
        box-shadow: 0 6px 32px rgba(233, 30, 99, 0.08);
    }

    .user-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: var(--primary-light);
        border-radius: 10px;
        padding: 18px 24px;
        margin-bottom: 28px;
        box-shadow: 0 2px 8px rgba(233, 30, 99, 0.06);
    }

    .user-info strong {
        color: var(--primary-dark);
    }

    .user-info a {
        color: var(--primary-dark);
        font-weight: bold;
        text-decoration: none;
        transition: color 0.2s;
    }

    .user-info a:hover {
        color: var(--primary);
        text-decoration: underline;
    }

    h1 {
        text-align: center;
        color: var(--primary);
        margin-bottom: 32px;
        font-size: 2.2em;
        letter-spacing: 1px;
    }

    .message {
        padding: 12px 18px;
        border-radius: 6px;
        margin-bottom: 18px;
        text-align: center;
        font-size: 1.1em;
    }

    .success-message {
        background: var(--success);
        color: var(--primary-dark);
        border-left: 5px solid var(--success-border);
    }

    .error-message {
        background: var(--error);
        color: #C62828;
        border-left: 5px solid var(--error-border);
    }

    .add-btn {
        display: block;
        width: fit-content;
        margin: 0 auto 32px auto;
        background: var(--primary);
        color: #fff;
        padding: 12px 28px;
        border-radius: 24px;
        font-size: 1.1em;
        font-weight: bold;
        text-decoration: none;
        box-shadow: 0 2px 8px rgba(233, 30, 99, 0.08);
        transition: background 0.2s, box-shadow 0.2s;
    }

    .add-btn:hover {
        background: var(--primary-dark);
        box-shadow: 0 4px 16px rgba(233, 30, 99, 0.12);
    }

    .days-list {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        padding: 0;
        margin: 0;
        list-style: none;
    }

    @media (max-width: 900px) {
        .days-list {
            grid-template-columns: 1fr;
        }
    }

    .day-item {
        background: var(--primary-light);
        border-radius: 14px;
        box-shadow: 0 2px 12px rgba(233, 30, 99, 0.07);
        padding: 24px 20px 20px 20px;
        position: relative;
        border-left: 6px solid var(--primary);
        display: flex;
        flex-direction: column;
        min-height: 180px;
    }

    .day-title {
        color: var(--primary-dark);
        font-size: 1.3em;
        margin-bottom: 8px;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .day-title i {
        color: var(--primary);
        font-size: 1.1em;
    }

    .day-date {
        color: var(--primary);
        font-weight: bold;
        margin-bottom: 8px;
        display: block;
        font-size: 1.1em;
    }

    .day-description {
        flex: 1;
        margin-bottom: 14px;
        color: #444;
    }

    .action-links {
        display: flex;
        gap: 12px;
        margin-top: auto;
    }

    .action-links a {
        color: var(--primary-dark);
        background: #fff;
        border: 1px solid var(--primary-dark);
        padding: 6px 18px;
        border-radius: 20px;
        font-weight: 500;
        text-decoration: none;
        transition: background 0.2s, color 0.2s;
        font-size: 1em;
    }

    .action-links a:hover {
        background: var(--primary-dark);
        color: #fff;
    }
</style>

<div class="container">
    <h1><i class="fas fa-calendar-day"></i> Danh sách ngày quan trọng</h1>

    <div class="user-info">
        <div>
            <i class="fas fa-user-circle"></i>
            Xin chào, <strong><?php echo htmlspecialchars($_SESSION['username']); ?> hãy thêm ngày quan trọng của mình nhé!</strong>
        </div>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="message success-message">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars($_SESSION['message']); ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="message error-message">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars($_SESSION['error']); ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <a href="/webdacn_quanlyclb/ImportantDay/add" class="add-btn">
        <i class="fas fa-plus"></i> Thêm ngày quan trọng
    </a>

    <ul class="days-list">
        <?php foreach ($days as $day): ?>
            <li class="day-item">
                <div class="day-title">
                    <i class="fas fa-star"></i>
                    <?php echo htmlspecialchars($day->title); ?>
                </div>
                <span class="day-date">
                    <i class="fas fa-calendar-alt"></i>
                    <?php echo htmlspecialchars($day->date); ?>
                </span>
                <div class="day-description">
                    <?php echo htmlspecialchars($day->description); ?>
                </div>
                <div class="action-links">
                    <a href="/webdacn_quanlyclb/ImportantDay/edit/<?php echo $day->id; ?>"><i class="fas fa-edit"></i> Sửa</a>
                    <a href="/webdacn_quanlyclb/ImportantDay/delete/<?php echo $day->id; ?>" onclick="return confirm('Bạn có chắc muốn xóa?');"><i class="fas fa-trash-alt"></i> Xóa</a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    
</div>
<?php include 'app/views/shares/footer.php'; ?>