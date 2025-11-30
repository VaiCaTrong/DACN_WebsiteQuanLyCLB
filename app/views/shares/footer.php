</main>
</div>
<footer class="footer bg-dark text-white pt-4 pb-3 mt-4">
    <div class="container-fluid px-lg-4 px-3">
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-3">
                <h5 class="text-uppercase mb-3" style="color: #E91E63;">HUTECH</h5>
                <ul class="list-unstyled small">
                    <li class="mb-1"><i class="fas fa-map-marker-alt me-2" style="color: #E91E63;"></i>475A Điện Biên Phủ, P.25, Q.Bình Thạnh</li>
                    <li class="mb-1"><i class="fas fa-phone-alt me-2" style="color: #E91E63;"></i>(028) 5445 7777</li>
                    <li class="mb-1"><i class="fas fa-envelope me-2" style="color: #E91E63;"></i>info@hutech.edu.vn</li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-6 mb-3">
                <h5 class="text-uppercase mb-3" style="color: #E91E63;">LIÊN KẾT</h5>
                <ul class="list-unstyled small">
                    <li class="mb-1"><a href="#" class="text-white text-decoration-none">Giới thiệu</a></li>
                    <li class="mb-1"><a href="#" class="text-white text-decoration-none">Tuyển sinh</a></li>
                    <li class="mb-1"><a href="#" class="text-white text-decoration-none">Đào tạo</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <h5 class="text-uppercase mb-3" style="color: #E91E63;">HỆ THỐNG</h5>
                <ul class="list-unstyled small">
                    <li class="mb-1"><a href="#" class="text-white text-decoration-none">Thư viện</a></li>
                    <li class="mb-1"><a href="#" class="text-white text-decoration-none">Sinh viên</a></li>
                    <li class="mb-1"><a href="#" class="text-white text-decoration-none">E-Learning</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <h5 class="text-uppercase mb-3" style="color: #E91E63;">BẢN ĐỒ</h5>
                <div class="map-container ratio ratio-16x9" style="height: 120px;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.123282255101!2d106.7123038147496!3d10.80195289230489!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317528a459cb43ab%3A0x6c3d29d370b52a7e!2zxJDhuqFpIGjhu41jIEPDtG5nIG5naOG7hyBUUC5IQ00gSFVURUNIIChUcsaw4budbmcgxJDhuqFpIGjhu41jIEvhu7kgdGh14bqtdCkgNDc1QSDEkGnhu4duIEJpw6puIFBo4bunLCBQLjI1LCBCw6xuaCBUaOG6oW5oLCBUaMOgbmggcGjhu5EgSOG7kyBDaMOtIE1pbmgsIFZp4buHdE5hbQ!5e0!3m2!1svi!2s!4v1678886561173!5m2!1svi!2s" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
        <hr class="my-3" style="border-color: rgba(255,255,255,0.1);">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start small">
                <p class="mb-0">© <?= date('Y') ?> HUTECH</p>
            </div>
            <div class="col-md-6 text-center text-md-end small">
                <p class="mb-0">Phát triển bởi Phạm Minh Trọng</p>
            </div>
        </div>
    </div>
</footer>
</div> 

<style>
    #ai-chat-toggle { position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; background: linear-gradient(135deg, #E91E63, #C2185B); border-radius: 50%; color: white; border: none; box-shadow: 0 4px 15px rgba(233, 30, 99, 0.4); z-index: 1060; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 24px; transition: transform 0.3s ease; }
    #ai-chat-toggle:hover { transform: scale(1.1) rotate(15deg); }
    #ai-chat-box { position: fixed; bottom: 90px; right: 20px; width: 350px; height: 500px; background-color: white; border-radius: 15px; box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2); z-index: 1060; display: none; flex-direction: column; overflow: hidden; border: 1px solid #eee; animation: slideInUp 0.3s ease; }
    @keyframes slideInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .ai-chat-header { background: linear-gradient(135deg, #E91E63, #C2185B); color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center; font-weight: 600; }
    .ai-chat-body { flex: 1; padding: 15px; overflow-y: auto; background-color: #f8f9fa; display: flex; flex-direction: column; gap: 10px; }
    .message { max-width: 85%; padding: 10px 15px; border-radius: 15px; font-size: 0.9rem; line-height: 1.5; word-wrap: break-word; }
    .message.user { align-self: flex-end; background-color: #E91E63; color: white; border-bottom-right-radius: 2px; }
    .message.ai { align-self: flex-start; background-color: white; border: 1px solid #eee; color: #333; border-bottom-left-radius: 2px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05); }
    .ai-chat-footer { padding: 10px; background-color: white; border-top: 1px solid #eee; display: flex; gap: 10px; }
    .ai-chat-footer input { border-radius: 20px; font-size: 0.9rem; }
    .ai-chat-footer button { border-radius: 50%; width: 38px; height: 38px; padding: 0; display: flex; align-items: center; justify-content: center; }
    .btn-apply-content { display: block; margin-top: 8px; font-size: 0.8rem; padding: 5px 12px; border-radius: 15px; background-color: #e3f2fd; color: #0d6efd; border: 1px solid #e3f2fd; transition: all 0.2s; cursor: pointer; width: fit-content; }
    .btn-apply-content:hover { background-color: #0d6efd; color: white; }
    .typing-indicator span { display: inline-block; width: 6px; height: 6px; background-color: #ccc; border-radius: 50%; animation: typing 1s infinite; margin: 0 2px; }
    .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
    .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
</style>

<button id="ai-chat-toggle" title="Trợ lý AI"><i class="fas fa-robot"></i></button>

<div id="ai-chat-box">
    <div class="ai-chat-header">
        <span><i class="fas fa-magic me-2"></i>HUTECH AI Assistant</span>
        <button type="button" class="btn-close btn-close-white" id="close-ai-chat" aria-label="Close"></button>
    </div>
    <div class="ai-chat-body" id="ai-chat-messages">
        <div class="message ai">Xin chào! Tôi có thể giúp gì cho bạn hôm nay?</div>
    </div>
    <div class="ai-chat-footer">
        <input type="text" id="ai-chat-input" class="form-control" placeholder="Nhập tin nhắn...">
        <button class="btn btn-primary" id="ai-chat-send" style="background-color: #E91E63; border-color: #E91E63;"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatToggle = document.getElementById('ai-chat-toggle');
        const headerAiTrigger = document.getElementById('header-ai-trigger'); // Nút trên Header
        const chatBox = document.getElementById('ai-chat-box');
        const closeChat = document.getElementById('close-ai-chat');
        const chatMessages = document.getElementById('ai-chat-messages');
        const chatInput = document.getElementById('ai-chat-input');
        const chatSend = document.getElementById('ai-chat-send');

        // Hàm bật/tắt box chat
        function toggleChat() {
            if (chatBox.style.display === 'none' || chatBox.style.display === '') {
                chatBox.style.display = 'flex';
                chatInput.focus();
            } else {
                chatBox.style.display = 'none';
            }
        }

        // Bắt sự kiện click nút tròn góc dưới
        chatToggle.addEventListener('click', toggleChat);

        // Bắt sự kiện click nút trên Header (nếu có)
        if (headerAiTrigger) {
            headerAiTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                toggleChat();
                // Reset về chào hỏi mặc định nếu đang trống
                if(chatMessages.children.length <= 1) {
                    addMessage("Xin chào! Tôi là trợ lý ảo HUTECH. Bạn cần giúp gì không?", "ai");
                }
            });
        }

        closeChat.addEventListener('click', () => {
            chatBox.style.display = 'none';
        });

        // Hàm Gửi tin nhắn
        // mode: 'chat' (thường) hoặc 'write' (viết bài)
        async function sendMessage(text = null, mode = 'chat') {
            const messageText = text || chatInput.value.trim();
            if (!messageText) return;

            addMessage(messageText, 'user');
            chatInput.value = '';
            const loadingId = addLoading();
            chatMessages.scrollTop = chatMessages.scrollHeight;

            try {
                const response = await fetch('/webdacn_quanlyclb/default/generateAI', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ 
                        prompt: messageText,
                        mode: mode // Gửi thêm tham số mode
                    })
                });

                const result = await response.json();
                removeLoading(loadingId);

                if (result.success) {
                    // Nếu là mode viết bài, hiện nút Apply
                    const showApply = (mode === 'write');
                    addMessage(result.content, 'ai', showApply); 
                } else {
                    addMessage("Xin lỗi, tôi gặp lỗi: " + result.message, 'ai');
                }
            } catch (error) {
                removeLoading(loadingId);
                addMessage("Lỗi kết nối server.", 'ai');
                console.error(error);
            }
        }

        // Sự kiện gửi từ ô input (Luôn là chat thường)
        chatSend.addEventListener('click', () => sendMessage(null, 'chat'));
        chatInput.addEventListener('keypress', (e) => { 
            if (e.key === 'Enter') sendMessage(null, 'chat'); 
        });

        function addMessage(text, sender, showApplyBtn = false) {
            const div = document.createElement('div');
            div.className = `message ${sender}`;
            
            if (sender === 'user') {
                div.innerHTML = text.replace(/\n/g, '<br>');
            } else {
                // AI trả về HTML sẵn rồi nên hiển thị luôn
                // Nếu là chat thường (không phải HTML), replace xuống dòng
                if (!text.includes('<p>') && !text.includes('<b>')) {
                    div.innerHTML = text.replace(/\n/g, '<br>');
                } else {
                    div.innerHTML = text;
                }
            }

            if (showApplyBtn) {
                const btn = document.createElement('button');
                btn.className = 'btn-apply-content';
                btn.innerHTML = '<i class="fas fa-check me-1"></i>Áp dụng vào bài viết';
                btn.dataset.content = text; 
                btn.onclick = function() {
                    applyContentToPost(this.dataset.content);
                };
                div.appendChild(btn);
            }
            chatMessages.appendChild(div);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function addLoading() {
            const id = 'loading-' + Date.now();
            const div = document.createElement('div');
            div.className = 'message ai typing-indicator';
            div.id = id;
            div.innerHTML = '<span></span><span></span><span></span>';
            chatMessages.appendChild(div);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            return id;
        }

        function removeLoading(id) {
            const el = document.getElementById(id);
            if (el) el.remove();
        }

        // Hàm Áp dụng nội dung (CKEditor / Textarea)
        window.applyContentToPost = function(content) {
            // Kiểm tra CKEditor 5
            if (window.mainEditor) {
                let htmlContent = content.replace(/^```html\s*/i, '').replace(/^```\s*/i, '').replace(/```$/i, '');
                htmlContent = htmlContent.replace(/\n/g, '<br>');
                const currentData = window.mainEditor.getData();
                if (currentData.trim() !== "") {
                    window.mainEditor.setData(currentData + "<p>&nbsp;</p>" + htmlContent);
                } else {
                    window.mainEditor.setData(htmlContent);
                }
                const editorElement = document.querySelector('.ck-editor');
                if (editorElement) {
                    editorElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    editorElement.style.border = "2px solid #28a745";
                    setTimeout(() => { editorElement.style.border = "none"; }, 1500);
                }
            } else {
                // Fallback textarea thường
                const contentTextarea = document.getElementById('content');
                if (contentTextarea) { // Sửa selector cho đúng ID ở create/edit.php
                    if (contentTextarea.value.trim() !== "") {
                        contentTextarea.value += "\n\n" + content;
                    } else {
                        contentTextarea.value = content;
                    }
                    contentTextarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    alert("Chức năng này chỉ hoạt động ở trang Tạo/Sửa bài viết.");
                }
            }
        };

        // Hàm gọi từ nút "Dùng AI viết bài" ở trang Create/Edit
        window.openAIChatWithPrompt = function(promptText) {
            chatBox.style.display = 'flex';
            addMessage(`Hãy viết bài dựa trên tiêu đề: "${promptText}"`, 'user');
            
            // Gửi với mode = 'write' để AI biết đường viết bài
            sendMessage(promptText, 'write');
        };
    });
</script>