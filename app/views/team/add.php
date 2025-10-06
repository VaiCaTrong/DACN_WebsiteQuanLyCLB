<?php include 'app/views/shares/header.php'; ?>
<link rel="stylesheet" href="/webdacn_quanlyclb/public/css/team/add.css">
<style>
    .form-label {
        font-weight: bold;
        color: #2d3436;
    }

    .btn-primary {
        background-color: #E91E63;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #C2185B;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(233, 30, 99, 0.3);
    }

    .form-control {
        border-color: #E91E63;
        border-width: 2px;
        border-radius: 8px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus {
        border-color: #C2185B;
        box-shadow: 0 0 5px rgba(233, 30, 99, 0.5);
        outline: none;
    }

    .container {
        max-width: 600px;
        margin-top: 2rem;
        padding: 20px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .container:hover {
        transform: translateY(-5px);
    }

    h2 {
        color: #E91E63;
        text-align: center;
        margin-bottom: 1.5rem;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 768px) {
        .container {
            margin-top: 1rem;
            padding: 15px;
            max-width: 100%;
        }

        h2 {
            font-size: 1.5rem;
        }
    }
</style>
<div class="container mt-5">
    <h2>Thêm đội nhóm mới</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Tên đội</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Mô tả</label>
            <textarea class="form-control" id="description" name="description"></textarea>
        </div>
        <div class="mb-3">
            <label for="quantity_user" class="form-label">Số lượng thành viên</label>
            <input type="number" class="form-control" id="quantity_user" name="quantity_user" value="1" required>
        </div>
        <div class="mb-3">
            <label for="talent" class="form-label">Tài năng</label>
            <input type="text" class="form-control" id="talent" name="talent">
        </div>
        <div class="mb-3">
            <label for="note" class="form-label">Ghi chú</label>
            <textarea class="form-control" id="note" name="note"></textarea>
        </div>
        <div class="mb-4">
            <label for="avatar_team" class="form-label">Ảnh đội</label>
            <input type="file" class="form-control" id="avatar_team" name="avatar_team">
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary">Thêm mới</button>
        </div>
    </form>
    
</div>
<?php include 'app/views/shares/footer.php'; ?>