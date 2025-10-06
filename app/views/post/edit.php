<style>
    body {
        background-color: #FEEBF3;
        padding: 0;
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
    }

    .container {
        margin-top: 40px;
        margin-bottom: 40px;
        max-width: 900px; /* Tăng chiều rộng tối đa */
        width: 90%; /* Chiếm 90% chiều rộng màn hình */
        padding: 30px; /* Tăng padding */
        background-color: #fff;
        border-radius: 20px; /* Bo góc lớn hơn */
        box-shadow: 0 10px 30px rgba(233, 30, 99, 0.15);
        animation: fadeIn 0.5s ease-in-out;
    }

    h1 {
        color: #E91E63;
        font-weight: bold;
        font-size: 2.2rem; /* Tăng kích thước tiêu đề */
        margin-bottom: 30px;
        text-align: center;
    }

    .card {
        border: none;
        border-radius: 16px; /* Bo góc lớn hơn */
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
    }

    .card-header {
        background: linear-gradient(to right, #F8BBD0, #F48FB1);
        color: white;
        border-top-left-radius: 16px !important;
        border-top-right-radius: 16px !important;
        font-weight: 600;
        padding: 18px 25px; /* Tăng padding */
        font-size: 1.3rem; /* Tăng kích thước font */
    }

    .card-body {
        padding: 30px; /* Tăng padding */
    }

    .form-label {
        font-weight: 600; /* Làm đậm nhãn */
        color: #555;
        font-size: 1.05rem; /* Tăng kích thước nhãn */
        margin-bottom: 10px;
    }

    .form-control {
        border-radius: 10px; /* Bo góc lớn hơn */
        border: 1px solid #ddd;
        transition: all 0.3s;
        padding: 12px 15px; /* Tăng padding */
        font-size: 1rem;
    }

    .form-control:focus {
        border-color: #E91E63;
        box-shadow: 0 0 0 0.25rem rgba(233, 30, 99, 0.25);
    }

    textarea.form-control {
        min-height: 200px; /* Tăng chiều cao textarea */
    }

    .btn-primary {
        background-color: #E91E63;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        padding: 12px 25px; /* Tăng padding */
        font-size: 1.05rem;
        transition: all 0.3s;
    }

    .btn-primary:hover {
        background-color: #C2185B;
        transform: translateY(-2px); /* Hiệu ứng nổi lên */
    }

    .btn-secondary {
        border-radius: 10px;
        font-weight: 600;
        padding: 12px 25px;
        font-size: 1.05rem;
        margin-left: 15px;
        transition: all 0.3s;
    }

    .btn-secondary:hover {
        transform: translateY(-2px);
    }

    .button-group {
        display: flex;
        justify-content: flex-start;
        margin-top: 25px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive cho mobile */
    @media (max-width: 768px) {
        .container {
            width: 95%;
            padding: 20px;
        }
        
        .button-group {
            flex-direction: column;
        }
        
        .btn-secondary {
            margin-left: 0;
            margin-top: 10px;
        }
    }
</style>

<body>
    <div class="container">
        <h1 class="mb-4">Chỉnh Sửa Bài Viết</h1>
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Thông Tin Bài Viết</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/webdacn_quanlyclb/default/edit/<?php echo $post['id']; ?>" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="title" class="form-label">Tiêu Đề Bài Viết</label>
                        <input type="text" class="form-control" id="title" name="title"
                            value="<?php echo htmlspecialchars($post['title']); ?>" required>
                    </div>
                    <div class="mb-4">
                        <label for="content" class="form-label">Nội Dung Chi Tiết</label>
                        <textarea class="form-control" id="content" name="content" rows="6"
                            required><?php echo htmlspecialchars($post['content']); ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="images" class="form-label">Hình Ảnh Đính Kèm</label>
                        <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*">
                        <div class="form-text">Bạn có thể chọn nhiều ảnh cùng lúc</div>
                    </div>
                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
                        <a href="/webdacn_quanlyclb" class="btn btn-secondary">Hủy Bỏ</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>