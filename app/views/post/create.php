<?php
require_once __DIR__ . '/../../helpers/SessionHelper.php';
SessionHelper::start();

if (!SessionHelper::isLoggedIn() || !in_array(SessionHelper::getRole(), ['admin', 'staff'])) {
    $_SESSION['error'] = "Bạn không có quyền truy cập trang này!";
    header("Location: /webdacn_quanlyclb");
    exit();
}

$database = new Database();
$db = $database->getConnection();
$teams = [];

if (SessionHelper::isAdmin()) {
    require_once __DIR__ . '/../../models/TeamModel.php';
    $teamModel = new TeamModel();
    $teams = $teamModel->getAllTeams();
} elseif (SessionHelper::isStaff()) {
    $teams = SessionHelper::getManagedClubs($db);
}
?>

<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css" />

<style>
    .ck-editor__editable_inline {
        min-height: 400px; /* Tăng chiều cao khung soạn thảo */
    }
    /* Ẩn textarea gốc đi để tránh vỡ giao diện */
    #content-textarea {
        display: none;
    }
</style>

<main class="container my-4" style="padding-top: 70px;">
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; background-color: #FFF9FB;">
        <div class="card-body p-4">
            <h2 class="text-center mb-4" style="color: #E91E63; font-weight: 600;">Thêm bài viết mới</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="/webdacn_quanlyclb/default/create" method="POST" enctype="multipart/form-data" id="postForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(SessionHelper::getCsrfToken()) ?>">

                <div class="mb-3">
                    <label for="title" class="form-label" style="color: #E91E63; font-weight: 600;">Tiêu đề</label>
                    <input type="text" class="form-control" id="title" name="title" required
                        oninput="this.value = this.value.toUpperCase()"
                        placeholder="NHẬP TIÊU ĐỀ BÀI VIẾT">
                </div>

                <div class="mb-3 text-end">
                    <button type="button" class="btn btn-sm btn-outline-primary" id="btn-ai-generate" style="border-radius: 20px;">
                        <i class="fas fa-magic me-1"></i> Dùng AI viết bài
                    </button>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label" style="color: #E91E63; font-weight: 600;">Nội dung bài viết</label>
                    
                    <div id="editor"></div>
                    
                    <textarea name="content" id="content-textarea"></textarea>
                </div>

                <div class="mb-3">
                    <label for="thumbnail" class="form-label" style="color: #E91E63; font-weight: 600;">
                        1. Ảnh đại diện bài viết (Bắt buộc)
                    </label>
                    <input type="file" class="form-control" id="thumbnail" name="thumbnail"
                        accept="image/*" required>
                </div>

                <div class="mb-3">
                    <label for="content_media" class="form-label" style="color: #E91E63; font-weight: 600;">
                        2. Ảnh/Video bổ sung
                    </label>
                    <input type="file" class="form-control" id="content_media" name="content_media[]" multiple
                        accept="image/*, video/*">
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label" style="color: #E91E63; font-weight: 600;">Thể loại</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="Thông báo">Thông báo</option>
                            <option value="Sự kiện">Sự kiện</option>
                            <option value="Chiêu sinh">Chiêu sinh</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="team_id" class="form-label" style="color: #E91E63; font-weight: 600;">Đội/CLB</label>
                        <select class="form-select" id="team_id" name="team_id">
                            <option value="">Không thuộc đội nào</option>
                            <?php foreach ($teams as $team): ?>
                                <option value="<?= $team['id'] ?>"><?= htmlspecialchars($team['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div id="sub-posts-container"></div>

                <div class="mb-4 text-center">
                    <button type="button" class="btn btn-outline-secondary w-100" id="addSubPostBtn" style="border-style: dashed; border-radius: 12px; padding: 10px;">
                        <i class="fas fa-plus-circle me-2"></i>Thêm bài viết phụ
                    </button>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary" id="submitBtn" style="background-color: #E91E63; border: none; border-radius: 50px; padding: 10px 40px; box-shadow: 0 4px 8px rgba(233, 30, 99, 0.3);">
                        <i class="fas fa-plus me-2"></i>Thêm bài viết
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script type="importmap">
    {
        "imports": {
            "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.js",
            "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.3.1/"
        }
    }
</script>

<script type="module">
    import {
        ClassicEditor,
        Essentials,
        Bold,
        Italic,
        Underline,
        Strikethrough,
        Font,
        Paragraph,
        List,
        Link,
        BlockQuote,
        Heading,
        Image,
        ImageToolbar,
        ImageCaption,
        ImageStyle,
        ImageResize,
        Table,
        TableToolbar,
        Alignment,
        Autoformat
    } from 'ckeditor5';

    let editorInstance;

    ClassicEditor
        .create(document.querySelector('#editor'), {
            plugins: [
                Essentials, Bold, Italic, Underline, Strikethrough, Font, 
                Paragraph, List, Link, BlockQuote, Heading, 
                Alignment, Table, TableToolbar, Autoformat
            ],
            toolbar: [
                'undo', 'redo', '|', 
                'heading', '|', 
                'bold', 'italic', 'underline', '|',
                'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                'alignment', 'bulletedList', 'numberedList', '|',
                'link', 'blockQuote', 'insertTable'
            ],
            placeholder: 'Nhập nội dung bài viết chi tiết tại đây...',
            language: 'vi'
        })
        .then(editor => {
            window.mainEditor = editor; // Lưu global để AI Chat dùng
            editorInstance = editor;
            console.log('CKEditor đã sẵn sàng!');
        })
        .catch(error => {
            console.error('Lỗi CKEditor:', error);
        });

    // --- XỬ LÝ SUBMIT FORM (QUAN TRỌNG) ---
    const form = document.getElementById('postForm');
    const submitBtn = document.getElementById('submitBtn');
    const contentTextarea = document.getElementById('content-textarea');

    form.addEventListener('submit', function(e) {
        // 1. Lấy dữ liệu từ CKEditor
        const editorData = editorInstance.getData();
        
        // 2. Kiểm tra rỗng (loại bỏ thẻ HTML rỗng như <p>&nbsp;</p>)
        const plainText = editorData.replace(/<[^>]*>/g, '').trim();
        
        if (!plainText && !editorData.includes('<img')) {
            e.preventDefault(); // Chặn gửi
            alert('Vui lòng nhập nội dung bài viết!');
            
            // Cuộn tới editor và nháy viền đỏ
            document.querySelector('.ck-editor').scrollIntoView({ behavior: 'smooth', block: 'center' });
            document.querySelector('.ck-editor').style.border = '1px solid red';
            return false;
        }

        // 3. Đổ dữ liệu vào textarea ẩn để PHP nhận được
        contentTextarea.value = editorData;

        // 4. Hiệu ứng loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
    });
</script>

<script>
    // Script thường (không phải module) cho các chức năng khác
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Logic nút AI
        const aiBtn = document.getElementById('btn-ai-generate');
        if (aiBtn) {
            aiBtn.addEventListener('click', function() {
                const titleInput = document.getElementById('title');
                const title = titleInput.value.trim();
                if (!title) {
                    alert('Vui lòng nhập tiêu đề trước!');
                    titleInput.focus();
                    return;
                }
                if (typeof window.openAIChatWithPrompt === "function") {
                    window.openAIChatWithPrompt(title);
                } else {
                    alert("Đang tải Chatbox, vui lòng đợi...");
                }
            });
        }

        // 2. Validate File
        window.validateFiles = function(input) { // Gán vào window để gọi từ HTML onchange
            const files = input.files;
            for (let file of files) {
                const isImage = file.type.startsWith('image/');
                const isVideo = file.type.startsWith('video/');
                if (!isImage && !isVideo) {
                    alert(`File "${file.name}" không hợp lệ!`);
                    input.value = ''; return;
                }
                const limit = isVideo ? 100 * 1024 * 1024 : 20 * 1024 * 1024;
                if (file.size > limit) {
                    alert(`File "${file.name}" quá lớn!`);
                    input.value = ''; return;
                }
            }
        }

        // 3. Logic thêm bài viết phụ
        let subPostIndex = 0;
        const addBtn = document.getElementById('addSubPostBtn');
        const container = document.getElementById('sub-posts-container');

        if (addBtn && container) {
            addBtn.addEventListener('click', function() {
                const fileInputId = `sub_file_${subPostIndex}`;
                const html = `
                    <div class="card mb-4 border-0 shadow-sm sub-post-item" style="background-color: #fff; border-left: 5px solid #6c757d !important;">
                        <div class="card-body position-relative">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Close" onclick="this.closest('.sub-post-item').remove()"></button>
                            <h5 class="card-title text-secondary mb-3"><i class="fas fa-layer-group me-2"></i>Nội dung phụ #${subPostIndex + 1}</h5>
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: #555;">Tiêu đề phụ</label>
                                <input type="text" class="form-control" name="sub_posts[${subPostIndex}][title]" required oninput="this.value = this.value.toUpperCase()" placeholder="VD: PHẦN TIẾP THEO...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: #555;">Nội dung chi tiết</label>
                                <textarea class="form-control" name="sub_posts[${subPostIndex}][content]" rows="4" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: #555;">Ảnh/Video minh họa</label>
                                <input type="file" class="form-control" id="${fileInputId}" name="sub_posts_media_${subPostIndex}[]" multiple accept="image/*, video/*" onchange="validateFiles(this)">
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
                subPostIndex++;
            });
        }
    });
</script>