<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/OODA/');
}
?>

    <link rel="stylesheet" href="<?= BASE_URL ?>/user/asset/styles/index.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/user/asset/styles/grid.css">
    
<!-- 🔹 Chính sách -->
<div class="policy-container">
    <div>
        <img src="<?= BASE_URL ?>/user/asset/img/policy1.png" alt="policy1">
        <div style="margin-left: -30px; padding: 10px; font-weight: 700; font-size: 20px;">Giao hàng miễn phí</div>
        <div style="margin-left:-20px; color:#444444; font-size: 14px;">Với đơn hàng hơn 300.000đ</div>
    </div>
    <div>
        <img src="<?= BASE_URL ?>/user/asset/img/policy2.png" alt="policy2">
        <div style="margin-left: -20px; padding: 10px; font-weight: 700; font-size: 20px;">Hỗ trợ 24/7</div>
        <div style="margin-left:-20px; color:#444444; font-size: 14px;">Nhanh chóng thuận tiện</div>
    </div>
    <div>
        <img src="<?= BASE_URL ?>/user/asset/img/policy3.jpg" alt="policy3">
        <div style="margin-left: -10px; padding: 10px; font-weight: 700; font-size: 20px;">Đổi trả trong 3 ngày</div>
        <div style="margin-left:-20px; color:#444444; font-size: 14px;">Hấp dẫn chưa từng có</div>
    </div>
    <div>
        <img src="<?= BASE_URL ?>/user/asset/img/policy4.png" alt="policy4">
        <div style="margin-left: -10px; padding: 10px; font-weight: 700; font-size: 20px;">Giá tiêu chuẩn</div>
        <div style="margin-left:-10px; color:#444444; font-size: 14px;">Tiết kiệm 10% giá thị trường</div>
    </div>
</div>

<!-- 🔹 Logo + thông tin -->
<div class="logo" style="color:#444444;padding-bottom:30px ; height: 150px;">
      <img src="<?= BASE_URL ?>/user/asset/img/seafruits-logo.png" alt="seafruits-logo">
    <div style="padding:10px;">
        <div>Hotline: 0123456789</div>
        <div>Email: AboutUs@gmail.com</div>
    </div>
</div>

<!-- 🔹 Footer chính -->
<div class="footer">
    <div class="footer-content">
        <div class="footer-section">
            <h3>Về chúng tôi</h3>
            <p>Sea Fruits - Nơi cung cấp trái cây tươi ngon, chất lượng cao với giá cả hợp lý.</p>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>

        <div class="footer-section">
            <h3>Liên kết nhanh</h3>
            <ul>
                <li><a href="<?= BASE_URL ?>index.php">Trang chủ</a></li>
                <li><a href="<?= BASE_URL ?>user/view/pages/introduce.php">Giới thiệu</a></li>
                <li><a href="<?= BASE_URL ?>user/view/pages/news.php">Tin tức</a></li>
                <li><a href="<?= BASE_URL ?>user/view/pages/contact.php">Liên hệ</a></li>

            </ul>
        </div>

        <div class="footer-section">
            <h3>Dịch vụ</h3>
            <ul>
                <li><a href="#">Giao hàng nhanh</a></li>
                <li><a href="#">Đổi trả dễ dàng</a></li>
                <li><a href="#">Thanh toán an toàn</a></li>
                <li><a href="#">Bảo hành chất lượng</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Liên hệ</h3>
            <ul class="contact-info">
                <li><i class="fas fa-map-marker-alt"></i> 123 Đường ABC, Quận 1, TP.HCM</li>
                <li><i class="fas fa-phone"></i> Hotline: 0123456789</li>
                <li><i class="fas fa-envelope"></i> Email: AboutUs@gmail.com</li>
            </ul>
        </div>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2024 Sea Fruits. All rights reserved.</p>
    </div>
</div>

</body>
</html>
