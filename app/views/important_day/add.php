<?php
include 'app/views/shares/header.php';
?>

<style>
    .add-container {
        max-width: 500px;
        margin: 40px auto;
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 6px 32px rgba(233,30,99,0.10);
        padding: 32px 28px 24px 28px;
    }
    h1 {
        color: #E91E63;
        text-align: center;
        margin-bottom: 28px;
        font-size: 2em;
        letter-spacing: 1px;
    }
    .add-form label {
        color: #E91E63;
        font-weight: 500;
        margin-bottom: 6px;
        display: block;
    }
    .add-form input[type="text"],
    .add-form input[type="date"],
    .add-form textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1.5px solid #E91E63;
        border-radius: 8px;
        margin-bottom: 18px;
        font-size: 1em;
        background: #FCE4EC;
        transition: border 0.2s;
    }
    .add-form input[type="text"]:focus,
    .add-form input[type="date"]:focus,
    .add-form textarea:focus {
        border-color: #C2185B;
        outline: none;
        background: #fff;
    }
    .add-form textarea {
        min-height: 80px;
        resize: vertical;
    }
    .add-form .btn-add {
        background: #E91E63;
        color: #fff;
        border: none;
        border-radius: 24px;
        padding: 12px 32px;
        font-size: 1.1em;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.2s, box-shadow 0.2s;
        box-shadow: 0 2px 8px rgba(233,30,99,0.08);
        display: block;
        margin: 0 auto 10px auto;
    }
    .add-form .btn-add:hover {
        background: #C2185B;
    }
    .add-form .back-link {
        display: block;
        text-align: center;
        margin-top: 8px;
        color: #E91E63;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }
    .add-form .back-link:hover {
        color: #C2185B;
        text-decoration: underline;
    }
    .error-list {
        background: #FFEBEE;
        border-left: 5px solid #F44336;
        color: #C62828;
        border-radius: 8px;
        padding: 12px 18px;
        margin-bottom: 18px;
        list-style: disc inside;
    }
</style>

<div class="add-container">
    <h1><i class="fas fa-plus"></i> Thêm ngày quan trọng</h1>
    <?php if (isset($errors) && !empty($errors)): ?>
        <ul class="error-list">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <form class="add-form" method="POST" action="/webdacn_quanlyclb/ImportantDay/add">
        <label for="title"><i class="fas fa-star"></i> Tiêu đề</label>
        <input type="text" id="title" name="title" placeholder="Tiêu đề" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>

        <label for="date"><i class="fas fa-calendar-alt"></i> Ngày</label>
        <input type="date" id="date" name="date" value="<?php echo isset($_POST['date']) ? htmlspecialchars($_POST['date']) : ''; ?>" required>

        <label for="description"><i class="fas fa-align-left"></i> Mô tả</label>
        <textarea id="description" name="description" placeholder="Mô tả"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>

        <button type="submit" class="btn-add"><i class="fas fa-plus"></i> Thêm</button>
        <a href="/webdacn_quanlyclb/ImportantDay" class="back-link"><i class="fas fa-arrow-left"></i> Quay lại</a>
    </form>
</div>

<?php
include 'app/views/shares/footer.php';
?>