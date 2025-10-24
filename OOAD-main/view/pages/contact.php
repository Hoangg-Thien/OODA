<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/OOAD-main/'); // sửa đúng tên folder gốc của bạn
}
include __DIR__ . '/../layout/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>asset/styles/contact.css">
<div class="contact-container" style="max-width: 800px; margin: 50px auto; text-align: center;">
    <h2>Liên Hệ</h2>
    
    <div class="contact-info">
    <p><i class="fas fa-envelope"></i><strong>Email:</strong> AboutUs@gmail.com</p>
    <p><i class="fas fa-phone"></i><strong>Hotline:</strong> 1234567890</p>
    <p><i class="fas fa-map-marker-alt"></i><strong>Địa chỉ:</strong> 273 Đ. An Dương Vương, Phường 2, Quận 5, TP. Hồ Chí Minh</p>
</div>


    <h3>Gửi tin nhắn cho chúng tôi</h3>
    <form class="contact-form" id="contactForm" 
          style="display: flex; flex-direction: column; gap: 15px; max-width: 600px; margin: 0 auto;">
        <input type="text" name="name" placeholder="Họ và tên" required 
               style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        <input type="email" name="email" placeholder="Email" required 
               style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        <input type="text" name="phone" placeholder="Số điện thoại" 
               style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
        <textarea name="message" rows="5" placeholder="Nội dung tin nhắn" required 
                  style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;"></textarea>
        <button type="submit" 
                style="background-color: green; color: white; border: none; padding: 12px; border-radius: 5px; cursor: pointer;">
            Gửi
        </button>
    </form>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
<script>
    document.getElementById('contactForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Ngăn chặn gửi form mặc định

        // Lấy dữ liệu từ form
        const name = this.name.value;
        const email = this.email.value;
        const phone = this.phone.value;
        const message = this.message.value;

        // Hiển thị thông báo (có thể thay thế bằng gửi AJAX)
        alert(`Cảm ơn ${name} đã liên hệ với chúng tôi! Chúng tôi sẽ phản hồi qua email: ${email}.`);

        // Reset form
        this.reset();
    });