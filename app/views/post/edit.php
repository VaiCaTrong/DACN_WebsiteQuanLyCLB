<?php
require_once __DIR__ . '/../../helpers/SessionHelper.php';
SessionHelper::start();

// Kiểm tra quyền
if (!SessionHelper::isLoggedIn() || !in_array(SessionHelper::getRole(), ['admin', 'staff'])) {
    $_SESSION['error'] = "Bạn không có quyền truy cập trang này!";
    header("Location: /webdacn_quanlyclb");
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Lấy danh sách CLB
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
    .edit-post-container {
        max-width: 900px; margin: 2rem auto; padding: 2rem;
        background-color: #fff; border-radius: 16px;
        box-shadow: 0 10px 30px rgba(233, 30, 99, 0.15); border-top: 5px solid #E91E63;
    }
    .ck-editor__editable_inline { min-height: 400px; }
    .media-preview-item {
        position: relative; display: inline-block; margin: 5px;
        border: 1px solid #ddd; border-radius: 8px; padding: 2px; background: #f8f9fa;
    }
    .media-preview-item img, .media-preview-item video {
        width: 100px; height: 100px; object-fit: cover; border-radius: 6px;
    }
    .delete-checkbox {
        position: absolute; top: -5px; right: -5px; width: 20px; height: 20px; cursor: pointer; accent-color: #dc3545;
    }
    /* Ẩn textarea gốc */
    #content-textarea { display: none; }
</style>

<main class="container" style="padding-top: 20px;">
    <div class="edit-post-container">
        <h2 class="text-center mb-4" style="color: #E91E63; font-weight: 700;">Chỉnh Sửa Bài Viết</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="/webdacn_quanlyclb/default/edit/<?php echo $post['id']; ?>" method="POST" enctype="multipart/form-data" id="editPostForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(SessionHelper::getCsrfToken()) ?>">

            <div class="mb-3">
                <label for="title" class="form-label fw-bold" style="color: #C2185B;">Tiêu đề bài viết</label>
                <input type="text" class="form-control" id="title" name="title" 
                       value="<?php echo htmlspecialchars($post['title']); ?>" required
                       oninput="this.value = this.value.toUpperCase()">
            </div>

            <div class="mb-3 text-end">
                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-ai-generate" style="border-radius: 20px;">
                    <i class="fas fa-magic me-1"></i> Dùng AI viết bài
                </button>
            </div>

            <div class="mb-4">
                <label for="content" class="form-label fw-bold" style="color: #C2185B;">Nội dung chi tiết</label>
                
                <div id="editor"><?php echo $post['content']; ?></div>
                
                <textarea name="content" id="content-textarea"></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Thể Loại</label>
                    <select class="form-select" id="category" name="category">
                        <option value="Thông báo" <?php echo ($post['category'] == 'Thông báo') ? 'selected' : ''; ?>>Thông báo</option>
                        <option value="Sự kiện" <?php echo ($post['category'] == 'Sự kiện') ? 'selected' : ''; ?>>Sự kiện</option>
                        <option value="Chiêu sinh" <?php echo ($post['category'] == 'Chiêu sinh') ? 'selected' : ''; ?>>Chiêu sinh</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Câu Lạc Bộ</label>
                    <select class="form-select" id="team_id" name="team_id">
                        <option value="">Không thuộc đội nào</option>
                        <?php foreach ($teams as $team): ?>
                            <option value="<?php echo $team['id']; ?>" <?php echo ($post['team_id'] == $team['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($team['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3 p-3 bg-light rounded border">
                <label class="form-label fw-bold mb-2">Ảnh/Video hiện tại (Tích vào ô để XÓA):</label>
                <div class="d-flex flex-wrap gap-2">
                    <?php if (!empty($images)): ?>
                        <?php foreach ($images as $img): ?>
                            <?php $isVid = (isset($img['type']) && $img['type'] == 'video') || preg_match('/\.(mp4|webm|mov)$/i', $img['image_path']); ?>
                            <div class="media-preview-item">
                                <?php if ($isVid): ?>
                                    <video src="/webdacn_quanlyclb/<?php echo htmlspecialchars($img['image_path']); ?>" muted></video>
                                <?php else: ?>
                                    <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($img['image_path']); ?>" alt="Img">
                                <?php endif; ?>
                                <input type="checkbox" name="delete_images[]" value="<?php echo $img['id']; ?>" class="delete-checkbox">
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-muted small fst-italic">Chưa có ảnh nào.</span>
                    <?php endif; ?>
                </div>
                <div class="mt-3">
                    <label class="form-label small text-primary fw-bold">Thêm ảnh/video mới:</label>
                    <input type="file" class="form-control" name="images[]" multiple accept="image/*, video/*" onchange="validateFiles(this)">
                </div>
            </div>

            <hr class="my-4" style="border-top: 2px dashed #E91E63;">

            <?php if (!empty($subPosts)): ?>
                <h5 class="mb-3 text-info"><i class="fas fa-edit me-2"></i>Chỉnh sửa nội dung phụ đã có</h5>
                <?php foreach ($subPosts as $index => $sub): ?>
                    <div class="card mb-3 border-info shadow-sm">
                        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                            <strong>Phần phụ #<?php echo $index + 1; ?></strong>
                            <div class="form-check form-switch">
                                <input class="form-check-input bg-danger" type="checkbox" name="delete_sub_posts[]" value="<?php echo $sub['id']; ?>" id="del_sub_<?php echo $sub['id']; ?>">
                                <label class="form-check-label text-danger fw-bold" for="del_sub_<?php echo $sub['id']; ?>">Xóa phần này</label>
                            </div>
                        </div>
                        <div class="card-body">
                            <input type="hidden" name="existing_subs[<?php echo $sub['id']; ?>][id]" value="<?php echo $sub['id']; ?>">
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Tiêu đề phụ</label>
                                <input type="text" class="form-control" name="existing_subs[<?php echo $sub['id']; ?>][title]" value="<?php echo htmlspecialchars($sub['title']); ?>" required oninput="this.value = this.value.toUpperCase()">
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-bold">Nội dung phụ</label>
                                <textarea class="form-control" name="existing_subs[<?php echo $sub['id']; ?>][content]" rows="4" required><?php echo htmlspecialchars($sub['content']); ?></textarea>
                            </div>
                            
                            <?php if (!empty($sub['images'])): ?>
                                <div class="mb-2">
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php foreach ($sub['images'] as $sImg): ?>
                                            <?php $sIsVid = (isset($sImg['type']) && $sImg['type'] == 'video') || preg_match('/\.(mp4|webm|mov)$/i', $sImg['image_path']); ?>
                                            <div class="media-preview-item" style="width: 60px; height: 60px;">
                                                <?php if ($sIsVid): ?>
                                                    <video src="/webdacn_quanlyclb/<?php echo htmlspecialchars($sImg['image_path']); ?>" style="width:100%; height:100%"></video>
                                                <?php else: ?>
                                                    <img src="/webdacn_quanlyclb/<?php echo htmlspecialchars($sImg['image_path']); ?>" style="width:100%; height:100%">
                                                <?php endif; ?>
                                                <input type="checkbox" name="delete_sub_images[]" value="<?php echo $sImg['id']; ?>" class="delete-checkbox">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="mb-2">
                                <label class="small text-muted">Thêm ảnh mới:</label>
                                <input type="file" class="form-control form-control-sm" name="existing_sub_media_<?php echo $sub['id']; ?>[]" multiple accept="image/*, video/*">
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div id="sub-posts-container"></div>

            <div class="mb-4 text-center">
                <button type="button" class="btn btn-outline-secondary w-100" id="addSubPostBtn" style="border-style: dashed; padding: 10px;">
                    <i class="fas fa-plus-circle me-2"></i>Thêm bài viết phụ mới
                </button>
            </div>

            <div class="text-center pb-5">
                <a href="/webdacn_quanlyclb/default/detail/<?php echo $post['id']; ?>" class="btn btn-secondary me-2">Hủy</a>
                <button type="submit" class="btn btn-primary" id="submitBtn" style="border-radius: 50px; padding: 10px 40px;">
                    Lưu Thay Đổi
                </button>
            </div>
        </form>
    </div>
</main>

<script type="importmap">
    { "imports": { "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.js", "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/43.3.1/" } }
</script>

<script type="module">
    import {
        ClassicEditor, Essentials, Bold, Italic, Underline, Strikethrough, Font, 
        Paragraph, List, Link, BlockQuote, Heading, Alignment, Table, TableToolbar, Autoformat
    } from 'ckeditor5';

    let editorInstance;

    ClassicEditor
        .create(document.querySelector('#editor'), {
            plugins: [ Essentials, Bold, Italic, Underline, Strikethrough, Font, Paragraph, List, Link, BlockQuote, Heading, Alignment, Table, TableToolbar, Autoformat ],
            toolbar: [ 'undo', 'redo', '|', 'heading', '|', 'bold', 'italic', 'underline', '|', 'fontSize', 'fontColor', 'fontBackgroundColor', '|', 'alignment', 'bulletedList', 'numberedList', '|', 'link', 'blockQuote', 'insertTable' ],
            language: 'vi'
        })
        .then(editor => {
            window.mainEditor = editor; 
            editorInstance = editor;
        })
        .catch(error => { console.error('Lỗi CKEditor:', error); });

    // --- XỬ LÝ SUBMIT FORM (ĐÃ SỬA LỖI) ---
    const form = document.getElementById('editPostForm');
    const submitBtn = document.getElementById('submitBtn');
    const contentTextarea = document.getElementById('content-textarea');

    form.addEventListener('submit', function(e) {
        // 1. Lấy dữ liệu từ CKEditor
        const editorData = editorInstance.getData();
        const plainText = editorData.replace(/<[^>]*>/g, '').trim();
        
        // 2. Kiểm tra rỗng (nếu không có chữ và không có thẻ img)
        if (!plainText && !editorData.includes('<img')) {
            e.preventDefault();
            alert('Nội dung không được để trống!');
            
            // Cuộn tới và báo đỏ
            document.querySelector('.ck-editor').scrollIntoView({ behavior: 'smooth', block: 'center' });
            document.querySelector('.ck-editor').style.border = '1px solid red';
            return false;
        }
        
        // 3. Đổ dữ liệu vào textarea ẩn để PHP nhận được
        contentTextarea.value = editorData;

        // 4. Loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';
    });
</script>

<script>
    // Validate File & Sub-posts Logic
    function validateFiles(input) {
        const files = input.files;
        for (let file of files) {
            if (!file.type.startsWith('image/') && !file.type.startsWith('video/')) {
                alert('Chỉ chấp nhận file Ảnh hoặc Video!'); input.value = ''; return;
            }
            if (file.size > 100 * 1024 * 1024) { alert('File quá lớn!'); input.value = ''; return; }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Nút AI
        const aiBtn = document.getElementById('btn-ai-generate');
        if (aiBtn) {
            aiBtn.addEventListener('click', function() {
                const title = document.getElementById('title').value.trim();
                if (!title) { alert('Vui lòng nhập tiêu đề trước!'); return; }
                if (typeof window.openAIChatWithPrompt === "function") {
                    window.openAIChatWithPrompt(title);
                } else {
                    alert("Đang tải Chatbox, vui lòng đợi...");
                }
            });
        }

        // Thêm bài viết phụ
        let subPostIndex = 0;
        const addBtn = document.getElementById('addSubPostBtn');
        const container = document.getElementById('sub-posts-container');
        if (addBtn && container) {
            addBtn.addEventListener('click', function() {
                const html = `
                    <div class="card mb-4 border-0 shadow-sm sub-post-item" style="background-color: #fff; border-left: 5px solid #6c757d !important;">
                        <div class="card-body position-relative">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Close" onclick="this.closest('.sub-post-item').remove()"></button>
                            <h5 class="card-title text-secondary mb-3">Nội dung phụ mới</h5>
                            <div class="mb-3"><label class="fw-bold">Tiêu đề phụ</label><input type="text" class="form-control" name="sub_posts[${subPostIndex}][title]" required oninput="this.value=this.value.toUpperCase()"></div>
                            <div class="mb-3"><label class="fw-bold">Nội dung chi tiết</label><textarea class="form-control" name="sub_posts[${subPostIndex}][content]" rows="4" required></textarea></div>
                            <div class="mb-3"><label class="fw-bold">Ảnh/Video</label><input type="file" class="form-control" name="sub_posts_media_${subPostIndex}[]" multiple accept="image/*, video/*" onchange="validateFiles(this)"></div>
                        </div>
                    </div>`;
                container.insertAdjacentHTML('beforeend', html);
                subPostIndex++;
            });
        }
    });
</script>