<footer class="footer bg-dark text-white pt-5 pb-4">
    <div class="container-fluid px-5">
        <div class="row">
            <!-- Cột thông tin liên hệ -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="text-uppercase mb-4" style="color: #E91E63;">TRƯỜNG ĐẠI HỌC CÔNG NGHỆ TP.HCM - HUTECH</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-map-marker-alt me-2" style="color: #E91E63;"></i>
                        <span>475A Điện Biên Phủ, P.25, Q.Bình Thạnh, TP.HCM.</span>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-phone-alt me-2" style="color: #E91E63;"></i>
                        <span>(028) 5445 7777</span>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-envelope me-2" style="color: #E91E63;"></i>
                        <span>info@hutech.edu.vn</span>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-globe me-2" style="color: #E91E63;"></i>
                        <a href="https://www.hutech.edu.vn" class="text-white text-decoration-none">www.hutech.edu.vn</a>
                    </li>
                </ul>
                <div class="social-icons mt-3">
                    <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="text-white me-3"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="text-white"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>

            <!-- Cột liên kết nhanh -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="text-uppercase mb-4" style="color: #E91E63;">LIÊN KẾT NHANH</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Giới thiệu</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Tuyển sinh</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Đào tạo</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Nghiên cứu</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Hợp tác</a></li>
                </ul>
            </div>

            <!-- Cột thông tin khác -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase mb-4" style="color: #E91E63;">HỆ THỐNG</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Thư viện điện tử</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Cổng thông tin sinh viên</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">E-Learning</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Email sinh viên</a></li>
                    <li class="mb-2"><a href="#" class="text-white text-decoration-none">Tra cứu văn bằng</a></li>
                </ul>
            </div>

            <!-- Cột bản đồ -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase mb-4" style="color: #E91E63;">BẢN ĐỒ</h5>
                <div class="map-container ratio ratio-16x9">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.122022227074!2d106.7122773152608!3d10.801826661739732!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x317528a459cb43ab%3A0x6c3d29d370b52a7e!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBDw7RuZyBuZ2jhu4cgVFAuSENNIC0gSFVURUNI!5e0!3m2!1svi!2s!4v1628762345678!5m2!1svi!2s"
                        style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>

        <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0">&copy; 2023 Đại học Công nghệ TP.HCM (HUTECH). Bảo lưu mọi quyền.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0">Phát triển bởi <span style="color: #E91E63;">Phạm Minh Trọng</span></p>
            </div>
        </div>
    </div>
    
</footer>

<style>
    .footer {
        background-color: #000;
        /* Đổi nền thành đen hoàn toàn */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        width: 100%;
        max-height: 300px;
        overflow: hidden;
        margin-top: 300px;
    }


    footer.footer {
        position: relative;
        z-index: 1101;
        /* Cao hơn sidebar (z-index: 1000) */
    }

    .footer h5 {
        font-size: 1.1rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 1.5rem;
        position: relative;
    }

    .footer h5::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -8px;
        width: 50px;
        height: 3px;
        background-color: #E91E63;
    }

    .footer ul li {
        transition: all 0.3s ease;
    }

    .footer ul li:hover {
        transform: translateX(5px);
    }

    .footer a:hover {
        color: #E91E63 !important;
        text-decoration: underline !important;
    }

    .social-icons a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background-color: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .social-icons a:hover {
        background-color: #E91E63;
        transform: translateY(-3px);
    }

    .map-container {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        margin-top: -20px;
        /* Dịch bản đồ lên trên */
    }
</style>